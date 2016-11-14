<?php

namespace AppBundle\Stuff;

use AppBundle\Entity\FlightType;
use AppBundle\Entity\SertAction;
use AppBundle\Entity\SertState;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\HydrationException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Sertificate;

class CertificateStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokens;

    /**
     * @var SmsStuff
     */
    private $smser;


    private $router;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, SmsStuff $smser, Router $router)
    {
        $this->em = $em;
        $this->tokens = $tokenStorage;
        $this->smser = $smser;
        $this->router = $router;
    }

    /**
     * certificates - массив сертификатов, упорядоченный по полю группировки
     * group_callback - функция, принимающая сертификат как параметр и возвращающая значение, по которому нужно сгрупировать
     * @param $certificates Sertificate[]
     * @param $group_callback callable
     * @return array
     */
    public function groupCertificatesBy($certificates, $group_callback)
    {
        $result = array();

        if (count($certificates) <= 0){
            return array();
        }

        $prev_dealer = $group_callback($certificates[0]);//$certificates[0]->getUser()->getUsername();
        $result[$prev_dealer] = array();

        foreach ($certificates as $certificate){
            if ($certificate->getUser()->getUsername() != $prev_dealer){
                $prev_dealer = $group_callback($certificate);//$certificate->getUser()->getUsername();
                $result[$prev_dealer] = array();
            }
            array_push($result[$prev_dealer], $certificate);
        }
        return $result;
    }

    /**
     * @param $mentor_id
     * @return Sertificate[]
     */
    public function getCertificatesByMentor($mentor_id, $state_id = -1)
    {
        $query_sql = "SELECT
            sertificate.ID_Sertificate,
            sertificate.name,
            sertificate.last_name,
            sertificate.phone_number,
            
            flight_type.ID_FlightType,
            flight_type.name AS `flight_name`,
            flight_type.price AS `flight_price`,
            flight_type.description_link AS `flight_link`,
            
            user.ID_User,
            user.username
        FROM
            sertificate
            
        LEFT OUTER JOIN 
            flight_type
        ON
            flight_type.ID_FlightType = sertificate.ID_FlightType
            
        LEFT OUTER JOIN
            `user`
        ON
            user.ID_User = sertificate.ID_User          
            
        WHERE
            (sertificate.ID_User IN (
                SELECT
                    user.ID_User
                FROM
                    user
                WHERE
                    user.ID_Mentor = :mentor_id
            ))";

        $params = array(
            "mentor_id" => $mentor_id,
        );
        //If user specified state_id, then we should add another condition to WHERE
        if ($state_id > 0 ){
            $query_sql .= "AND (sertificate.ID_SertState = :state_id)";
            $params["state_id"] = $state_id;
        }

        $query_sql .= " ORDER BY username, ID_Sertificate";

        //Map results to entities
        $rsm = new ResultSetMapping();

        $rsm->addEntityResult("AppBundle:Sertificate", "certificate");
        $rsm->addFieldResult("certificate", "ID_Sertificate", "ID_Sertificate");
        $rsm->addFieldResult("certificate", "name", "name");
        $rsm->addFieldResult("certificate", "last_name", "last_name");
        $rsm->addFieldResult("certificate", "phone_number", "phone_number");

        $rsm->addJoinedEntityResult("AppBundle:FlightType", "flight_type", "certificate", "ID_FlightType");
        $rsm->addFieldResult("flight_type", "ID_FlightType", "ID_FlightType");
        $rsm->addFieldResult("flight_type", "flight_name", "name");
        $rsm->addFieldResult("flight_type", "flight_price", "price");
        $rsm->addFieldResult("flight_type", "flight_link", "description_link");

        $rsm->addJoinedEntityResult("AppBundle:User", "user", "certificate", "ID_User");
        $rsm->addFieldResult("user", "ID_User", "ID_User");
        $rsm->addFieldResult("user", "username", "username");

        //Execute query and fetch results
        $query = $this->em->createNativeQuery($query_sql, $rsm);
        $query->setParameters($params);
        $query_result = $query->getResult();

        return $query_result;
    }

    /**
     * @param $state_id
     * @return array
     */
    public function getAvailableActions($state_id)
    {
        /**
         * @var $query_result SertAction[]
         */
        $query_sql = "SELECT
            sertaction.ID_ActionName AS `ID_ActionName`,
            sertaction.display_name AS `display_name`,
            sertaction.icon AS `icon`
        FROM
            groups_actions
        INNER JOIN(
            SELECT
                *
            FROM
                states_actions
            WHERE
                states_actions.ID_SertState = :ID_SertState
        ) AS allowed_actions
        ON
            allowed_actions.ID_ActionName = groups_actions.ID_ActionName
        LEFT JOIN
            sertaction
        ON
            groups_actions.ID_ActionName = sertaction.ID_ActionName
        WHERE
            ID_UserGroup = :ID_UserGroup
        ORDER BY 
            sertaction.ID_ActionName";

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult("AppBundle:SertAction", "action");
        $rsm->addFieldResult("action", "ID_ActionName", "action_name");
        $rsm->addFieldResult("action", "display_name", "display_name");
        $rsm->addFieldResult("action", "icon", "icon");

        $query = $this->em->createNativeQuery($query_sql, $rsm);
        $query->setParameters(array(
            "ID_SertState" => $state_id,
            "ID_UserGroup" => $this->tokens->getToken()->getUser()->getUserGroup()->getIDUserGroup(),
        ));
        $query_result = $query->getResult();

        return $query_result;
    }

    /**
     * @param $certificate Sertificate
     */
    public function activateCertificate($certificate)
    {
        /** @var $activ_state SertState */
        $activ_state = $this->em->getRepository("AppBundle:SertState")->find(5);
        $certificate->setIDSertState($activ_state);

        $this->em->persist($certificate);
        $this->em->flush();

        $sms_text = "Ваш сертификат №".$certificate->getIDSertificate()." активирован! До встречи в небе =]";
        $sms_phone = $certificate->getPhoneNumber();
        $sms_stuff = $this->smser->sendSms($sms_phone, $sms_text);
    }

    /**
     * @param $certificates Sertificate[]
     */
    public function activateCertificates($certificates)
    {
        foreach ($certificates as $certificate){
            $this->activateCertificate($certificate);
        }
        $this->em->flush();
    }

    /**
     * @param $data array
     */
    public function groupProcessCertificates($data)
    {
        /** @var $action SertAction */
        $action = $data["action"];
        if ($action == null){
            return;
        }

        /** @var $certificates Sertificate[] */
        $certificates = $data["certificates"];
        if (count($certificates) <= 0){
            return;
        }

        switch($action->getActionName()){
            case "activate":
                $this->activateCertificates($certificates);
                break;
        }
    }

    /**
     * @param $user User
     * @return array
     */
    public function getCertificatesWithActions($user)
    {
        $query_sql = "SELECT
            sertificate.ID_Sertificate,
            CONCAT_WS(\" \", sertificate.name, sertificate.last_name) AS `client_name`,
            sertificate.phone_number,
            flight_type.name AS `flight_type`,
            flight_type.price AS `flight_price`,
            flight_type.description_link AS `flight_link`,
            sertificate_state.ID_SertState,
            sertificate_state.name AS `state`
        FROM
            sertificate
        LEFT OUTER JOIN
            flight_type
        ON
            flight_type.ID_FlightType = sertificate.ID_FlightType
        LEFT OUTER JOIN
            sertificate_state
        ON
            sertificate_state.ID_SertState = sertificate.ID_SertState
        WHERE
            (sertificate.ID_User = :user_id) AND
            (sertificate.ID_SertState NOT IN (1))";

        $query = $this->em->getConnection()->prepare($query_sql);
        $query->execute(array(
            "user_id" => $user->getIDUser()
        ));
        /** @var $certificates array */
        $certificates = $query->fetchAll();

        /** @var $action_list SertAction[] */
        $action_list = $user->getIDUserGroup()->getAllowedActions()->toArray();

        /**
         * $action_groups - массив массивов действий. Первый индекс - номер состояния сертификата
         * Каждый элемент этого массива представляет собой массив доступных действий над сертификатом
         * в указанном состоянии
         *
         * @var $action_groups array
         */
        $action_groups = array();

        foreach ($certificates as $key => $certificate){
            $state_id = $certificate["ID_SertState"];
            if (!isset($action_groups[$state_id])) {
                $action_groups[$state_id] = array();
                foreach ($action_list as $action){
                    //Сформировать массив ID состояний
                    $allowed_ids = $action->getStates()->map(function($state){
                        /** @var $state SertState */
                        return $state->getIDSertState();
                    });

                    if (in_array($state_id, $allowed_ids->toArray())){
                        array_push($action_groups[$state_id], $action);
                    }
                }
            }
            $certificates[$key]["actions"] = $action_groups[$state_id];
        }

        return $certificates;
    }

    /**
     * @param array $ids
     * @param array $field_names
     * @param array $field_values
     * @return Sertificate
     */
    public function CertEdition(array $ids, array $field_names, array $field_values){
        $cert_list = [];
        foreach($ids AS $id) {
            /** @var  $cert Sertificate */
            $cert = $this->em->getRepository("AppBundle:Sertificate")->find($id);
            if (in_array("name", $field_names)) {
                $cert->setName($field_values[array_search("name", $field_names)]);
            }
            if (in_array("last_name", $field_names)) {
                $cert->setLastName($field_values[array_search("last_name", $field_names)]);
            }
            if (in_array("phone_number", $field_names)) {
                $cert->setPhoneNumber($field_values[array_search("phone_number", $field_names)]);
            }
            if (in_array("id_flight_type", $field_names)) {
                $flight_type = $this->em->getRepository("AppBundle:FlightType")->find($field_values[array_search("id_flight_type", $field_names)]);
                $cert->setFlightType($flight_type);
            }
            if (in_array("id_cert_state", $field_names)) {
                $cert_state = $this->em->getRepository("AppBundle:SertState")->find($field_values[array_search("id_cert_state", $field_names)]);
                $cert->setSertState($cert_state);
            }
            if (in_array("id_cert_action", $field_names)) {
                if ($field_values[array_search("id_cert_state", $field_names)] == "activate")
                    $this->activateCertificate($cert);
            }
            if (in_array("use_time", $field_names)) {
                $cert->setUseTime(date_create(date("d-m-Y H:i:s T", $field_values[array_search("use_time", $field_names)])));
            }
            if (in_array("user_id", $field_names)) {
                $user = $this->em->getRepository("AppBundle:User")->find($field_values[array_search("user_id", $field_names)]);
                $cert->setUser($user);
            }
            array_push($cert_list, $cert);
        }
        return $cert_list;
    }

    /**
     * @param Sertificate $cert
     * @param array $fields
     * @return array
     */
    public function CertToArray(Sertificate $cert, array $fields){
        $cert_info = [];
        if ($cert != null){
            if (in_array("name", $fields)){
                $cert_info["name"] = $cert->getName();
            }
            if (in_array("last_name", $fields)){
                $cert_info["last_name"] = $cert->getLastName();
            }
            if (in_array("phone_number", $fields)){
                $cert_info["phone_number"] = $cert->getPhoneNumber();
            }
            if (in_array("flight_type", $fields) && ($cert->getFlightType())){
                $cert_info["flight_type"] = $cert->getFlightType()->getName();
            }
            if (in_array("cert_state", $fields) && ($cert->getSertState())){
                $cert_info["cert_state"] = $cert->getSertState()->getName();
            }
            if (in_array("use_time", $fields)){
                $cert_info["use_time"] = $cert->getUseTime();
            }
            if (in_array("user_id", $fields) && ($cert->getUser())){
                $cert_info["user_id"] = $cert->getUser()->getIDUser();
            }
            if (in_array("user_login", $fields) && ($cert->getUser())){
                $cert_info["user_login"] = $cert->getUser()->getUsername();
            }
            if (in_array("ID_Sertificate", $fields)){
                $cert_info["ID_Sertificate"] = $cert->getIDSertificate();
            }
            if (in_array("cert_link", $fields)){
                $cert_link = $this->router->generate('certificate_view',["certificate" => $cert->getIDSertificate()]);
                $cert_info["cert_link"] = $cert_link;
            }
            if (in_array("price", $fields)){
                $cert_info["price"] = ($cert->getFlightType() == null)?0:$cert->getFlightType()->getPrice();
            }
        }
        return $cert_info;
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $fields
     * @return array
     */
    public function GetCertArray(array $criteria, array $sort, array $fields){
        $certs = $this->em->getRepository("AppBundle:Sertificate")->findBy($criteria, $sort);
        $cert_list = [];
        if ($certs != null){
            foreach($certs AS $cert){
                if ($cert != null) {
                    $cert_info = $this->CertToArray($cert, $fields);
                    array_push($cert_list, $cert_info);
                }
            }
        }
        return $cert_list;
    }

    public function objectConvert($object){
        $criteria = [];
        /** @var  $user User*/
        $used = false;
        $user = $this->tokens->getToken()->getUser();
        $user_roles = (array) $this->tokens->getToken()->getUser();
        $user_ids = array();
        if (in_array("ROLE_ADMIN", $user_roles)){
            $used = false;
            /** @var  $users User[]*/
            $users = $this->em->getRepository("AppBundle:User")->findBy([],[]);
            foreach($users AS $dealer){
                array_push($user_ids, $dealer->getIDUser());
            }
        } elseif (in_array("ROLE_MANAGER", $user_roles) and !($used)){
            $used = true;
            /** @var  $dealers User[]*/
            $dealers = $this->em->getRepository("AppBundle:User")->findBy(array("ID_Mentor" => $user));
            foreach($dealers AS $dealer){
                array_push($user_ids, $dealer->getIDUser());
            }
            array_push($user_ids, $user->getIDUser());
        } elseif (in_array("ROLE_DEALER", $user_roles) and !($used)){
            array_push($user_ids, $user->getIDUser());
        }
        if ((isset($object["ID_Sertificate"])?$object["ID_Sertificate"]:null) != null) $criteria["ID_Sertificate"] = $object["ID_Sertificate"];
        if ((isset($object["name"])?$object["name"]:null) != null) {
            $sql_query = 'SELECT DISTINCT sertificate.name FROM sertificate';
            $query = $this->em->getConnection()->prepare($sql_query);
            $query->execute();
            $user_last_names = $query->fetchAll();
            $patterns = $object["name"];
            $criteria["name"] = [];
            foreach($patterns AS $pattern){
                foreach($user_last_names AS $user_last_name){
                    if (stripos($user_last_name['name'], $pattern) !== false){
                        array_push($criteria["name"], $user_last_name['name']);
                    }
                }
            }
        }
        if ((isset($object["last_name"])?$object["last_name"]:null) != null) {
            $sql_query = 'SELECT DISTINCT sertificate.last_name FROM sertificate';
            $query = $this->em->getConnection()->prepare($sql_query);
            $query->execute();
            $user_last_names = $query->fetchAll();
            $patterns = $object["last_name"];
            $criteria["last_name"] = [];
            foreach($patterns AS $pattern){
                foreach($user_last_names AS $user_last_name){
                    if (stripos($user_last_name['last_name'], $pattern) !== false){
                        array_push($criteria["last_name"], $user_last_name['last_name']);
                    }
                }
            }
        }
        if ((isset($object["phone_number"])?$object["phone_number"]:null) != null) $criteria["phone_number"] = $object["phone_number"];
        if ((isset($object["use_time"])?$object["use_time"]:null) != null) $criteria["use_time"] = strtotime($object["use_time"]);
        if ((isset($object["ID_FlightType"])?$object["ID_FlightType"]:null) != null) $criteria["ID_FlightType"] = $this->em->getRepository("AppBundle:FlightType")->findBy(array("ID_FlightType" => $object["ID_FlightType"]));
        if ((isset($object["ID_SertState"])?$object["ID_SertState"]:null) != null) $criteria["ID_SertState"] = $this->em->getRepository("AppBundle:SertState")->findBy(array("ID_SertState" => $object["ID_SertState"]));
        if ((isset($object["ID_CertificatePack"])?$object["ID_CertificatePack"]:null) != null) $criteria["ID_CertificatePack"] = $this->em->getRepository("AppBundle:CertificatePack")->findBy(array("ID_CertificatePack" => $object["ID_CertificatePack"]));
        if ((isset($object["ID_User"])?$object["ID_User"]:null) != null) {
            $user_input = $object["ID_User"];
            $right_users = [];
            foreach($user_input AS $users_el){
                if (in_array($users_el, $user_ids)){
                    array_push($right_users, $users_el);
                }
            }
            if (count($right_users) != 0)
                $criteria["ID_User"] = $this->em->getRepository("AppBundle:User")->findBy(array("ID_User" => $right_users));
        } elseif (count($user_ids) != 0) {
            $criteria["ID_User"] = $user_ids;
        }
        return $criteria;
        //2,3,4,5
    }

    /**
     * @param $object
     * @return array
     */
    public function SortObjectConvert($object){
        $sort = [];
        if ((isset($object["ID_User"])?$object["ID_User"]:null) != null) $sort["ID_User"] = implode($object["ID_User"]);
        if ((isset($object["ID_Sertificate"])?$object["ID_Sertificate"]:null) != null) $sort["ID_Sertificate"] = implode($object["ID_Sertificate"]);
        if ((isset($object["name"])?$object["name"]:null) != null) $sort["name"] = implode($object["name"]);
        if ((isset($object["last_name"])?$object["last_name"]:null) != null) $sort["last_name"] = implode($object["last_name"]);
        if ((isset($object["phone_number"])?$object["phone_number"]:null) != null) $sort["phone_number"] = implode($object["phone_number"]);
        if ((isset($object["ID_FlightType"])?$object["ID_FlightType"]:null) != null) $sort["ID_FlightType"] = implode($object["ID_FlightType"]);
        if ((isset($object["ID_SertState"])?$object["ID_SertState"]:null) != null) $sort["ID_SertState"] = implode($object["ID_SertState"]);
        if ((isset($object["use_time"])?$object["use_time"]:null) != null) $sort["use_time"] = implode($object["use_time"]);
        return $sort;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function GetCertArrayFromRequest(Request $request){
        $criteria = $this->objectConvert((array)json_decode($request->request->get('criteria')));
        $fields = json_decode($request->request->get('field_names'));
        $sort = $this->SortObjectConvert((array)json_decode($request->request->get('sort')));
        if ($sort == null) $sort = [];
        if ($criteria == null) $criteria = [];
        if ($fields == null) $fields = [];
        $cert = $this->GetCertArray($criteria, $sort, $fields);
        return $cert;
    }

    /**
     * @return array
     */
    public function GetCertUserLogins($cert_state) {
        $sql_query = 'SELECT DISTINCT sertificate.ID_User FROM sertificate WHERE sertificate.ID_SertState = :id_state';
        $query = $this->em->getConnection()->prepare($sql_query);
        $query->execute(array('id_state' => $cert_state));
        $user_ids = $query->fetchAll();
        $user_list = array();
        if (count($user_ids) != 0) {
            foreach ($user_ids AS $user_id) {
                if ($user_id != null) {
                    /** @var  $user User */
                    $user = $this->em->getRepository("AppBundle:User")->find($user_id);
                    $user_info = array();
                    $user_info["name"] = $user->getUsername();
                    $user_info["id"] = $user->getIDUser();
                    array_push($user_list, $user_info);
                }
            }
        }
        return $user_list;
    }

    /**
     * @return array
     */
    public function GetCertStates() {
        $query = $this->em->createQuery('SELECT DISTINCT sertificate.ID_SertState FROM sertificate');
        $cert_state_ids = $query->getResult();
        $cert_state_list = array();
        foreach ($cert_state_ids AS $cert_state_id){
            /** @var  $cert_state SertState*/
            $cert_state = $this->em->getRepository("AppBundle:SertState")->find($cert_state_id);
            $cert_state_info = array();
            $cert_state_info["name"] = $cert_state->getName();
            $cert_state_info["id"] = $cert_state->getIDSertState();
            array_push($cert_state_list, $cert_state_info);
        }
        return $cert_state_list;
    }

    /**
     * @return array
     */
    public function GetCertFlightTypes($cert_state) {
        $sql_query = 'SELECT DISTINCT sertificate.ID_FlightType FROM sertificate WHERE sertificate.ID_SertState = :id_state';
        $query = $this->em->getConnection()->prepare($sql_query);
        $query->execute(array('id_state' => $cert_state));
        $cert_flight_type_ids = $query->fetchAll();
        $cert_flight_type_list = array();
        if (count($cert_flight_type_ids) != 0) {
            foreach ($cert_flight_type_ids AS $cert_flight_type_id) {
                /** @var  $cert_flight_type FlightType */
                if ($cert_flight_type_id['ID_FlightType'] != null) {
                    $cert_flight_type = $this->em->getRepository("AppBundle:FlightType")->find($cert_flight_type_id['ID_FlightType']);
                    $cert_flight_type_info = array();
                    $cert_flight_type_info["name"] = $cert_flight_type->getName();
                    $cert_flight_type_info["id"] = $cert_flight_type->getIDFlightType();
                    array_push($cert_flight_type_list, $cert_flight_type_info);
                }
            }
        }
        return $cert_flight_type_list;
    }
}