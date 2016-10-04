<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 10.09.2016
 * Time: 12:53
 */
namespace AppBundle\Controller;

use AppBundle\Stuff\CertificateStuff;
use AppBundle\DataClasses\CertEdition;
use AppBundle\DataClasses\UserEdition;
use AppBundle\DataClasses\UserIDCheck;
use AppBundle\DataClasses\CertCreation;
use AppBundle\DataClasses\CertAttachment;
use AppBundle\Entity\User;
use AppBundle\Entity\GroupParam;
use AppBundle\Entity\ParamValue;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\SertState;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AdminController extends Controller{

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin", name="admin_index")
     */
    public function indexAction(Request $request){
        return $this->render("admin/index.html.twig");
    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/user_table", name = "user_table")
     *
     * @Method("GET")
     */
    public function showUserTableAction(Request $request){
        /** @var $em EntityManager */
        $em = $this -> getDoctrine() -> getManager();
        /** @var $users User[] */
        $users = $em->getRepository("AppBundle:User")->findBy([],['username'=>'ASC']);

        $users_array = [];
        foreach($users as $user){
            $user_array_item = [];
            $user_array_item["userInfoLink"] = $this->get('router')->generate('user_info', ['ID_User' => $user->getIDUser()]);
            $user_array_item["ID_User"] = $user->getIDUser();
            $user_array_item["username"] = $user->getUsername();
            $user_array_item["email"] = $user->getEmail();
            $user_array_item["role"] = $user->getIDUserGroup()->getDisplayName();

            array_push($users_array, $user_array_item);
        }
        $unattached_certs = count($em->getRepository("AppBundle:Sertificate")->findBy(array('ID_SertState' => 0)));
        $response = new Response();
        $Request_output = array(
            'users' => $users_array,
            'error_msg' => array(),
            'error_param' => array(),
            'unattached_certs' => $unattached_certs
        );
        $response->setContent(json_encode($Request_output));
        $response->headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/attach", name = "attach_cert")
     *
     * @Method("GET")
     */
    public function showUserCertAction(Request $request){
        $response = new Response();
        $ID_User = $request->query->get('user_id');
        $request_data = new UserIDCheck();
        $request_data->setUserID($ID_User);
        $validator = $this->get('validator');
        $errors = $validator->validate($request_data);
        $att_certs = [];
        $unatt_certs = [];
        if (count($errors) == 0){
            /** @var $em EntityManager */
            $em = $this->getDoctrine()->getManager();
            /** @var $certs Sertificate[]*/
            $certs = $em->getRepository("AppBundle:Sertificate")->findBy(array('ID_User' => $ID_User));
            foreach($certs as $cert){
                $cert_array_item = [];
                $cert_array_item["ID_certificate"] = $cert->getIDSertificate();
                $cert_array_item["CertState"] = $cert->getSertState()->getName();
                array_push($att_certs, $cert_array_item);
            }
            $certs = $em->getRepository("AppBundle:Sertificate")->findBy(array('ID_SertState' => 0), array('ID_Sertificate' => 'DESC'));
            foreach($certs as $cert){
                $cert_array_item = [];
                $cert_array_item["ID_certificate"] = $cert->getIDSertificate();
                array_push($unatt_certs, $cert_array_item);
            }
        }
        $Request_output = array(
            'unatt_certs' => $unatt_certs,
            'att_certs' => $att_certs,
            'error_msg' => array(),
            'error_param' => array()
        );
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }

        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Some comment goes here
     * @param Request $request
     * @Route("/admin/cert_attachment", name = "cert_attachment")
     * @Method("POST")
     * @return Response
     */
    public function attachCertAction(Request $request){
        $response = new Response();
        $attachInfo = new CertAttachment();
        $attachInfo
            ->setCertIds(json_decode($request->request->get('cert_ids')))
            ->setUserId($request->request->get('user_id'));
        $validator = $this->get('validator');
        $errors = $validator->validate($attachInfo);
        $em = $this->getDoctrine()->getManager();
        $Request_output = array(
            'error_msg' => array(),
            'error_param' =>array()
        );
        if(count($errors) == 0) {
            /** @var $userAttachTo User */
            $userAttachTo = $this->getDoctrine()->getRepository("AppBundle:User")->find($attachInfo->getUserId());
            /** @var  $certState SertState*/
            $certState = $this->getDoctrine()->getRepository("AppBundle:SertState")->find(1);
            foreach($attachInfo->getCertIds() as $cert_id){
                /** @var  $cert Sertificate*/
                $cert = $em->getRepository("AppBundle:Sertificate")->find($cert_id);
                $cert->setIDUser($userAttachTo)->setIDSertState($certState);
                $em->persist($cert);
            }
            $em->flush();
            array_push($Request_output, 'success');
        }
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }
        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @Route("/admin/cert_creation", name = "cert_creation")
     * @Method("POST")
     * @return Response
     */
    public function createCertAction(Request $request){
        $response = new Response();
        $certs = new CertCreation();
        $user = $this->getUser();
        $certs->setCertIdArray(json_decode($request->request->get('cert_ids')));
        $validator = $this->get('validator');
        $errors = $validator->validate($certs);
        $em = $this->getDoctrine()->getManager();
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        if(count($errors) == 0){
            foreach($certs->getCertIdArray() as $cert_id) {
                /** @var $cert_state SertState */
                $cert_state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find(0);
                $cert = new Sertificate();
                $cert->
                    setIDSertificate($cert_id)->
                    setIDSertState($cert_state)->
                    setIDUser($user);
                $em->persist($cert);
            }
            $em->flush();
            array_push($Request_output, 'success');
        }
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'],$error->getInvalidValue());
        }
        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/user_edit", name = "user_edit")
     *
     * @Method("GET")
     */
    public function showUserParams(Request $request){
        $user_id = new UserIDCheck();
        $user_id->setUserID($request->query->get('user_id'));
        $response = new Response();
        /** @var  $users User[]*/
            $em = $this->getDoctrine()->getManager();
        /** @var  $em EntityManager */
        $validator = $this->get('validator');
        $errors = $validator->validate($user_id);
        $user_info_array = [];
        if (count($errors) == 0) {
            $users = $em->getRepository("AppBundle:User")->findBy(array('ID_User' => $user_id->getUserID()));
            foreach ($users as $user) {
                $user_info_array["username"] = $user->getUsername();
                $user_info_array["email"] = $user->getEmail();
                $user_info_array["group_name"] = $user->getIDUserGroup()->getDisplayName();
            }
        }
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array(),
            'user_info' => $user_info_array
        );
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'],$error->getInvalidValue());
        }
        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/user_group_edit", name = "user_group_edit")
     *
     * @Method("GET")
     */
    public function showGroupParams(Request $request){
        $request_input = new UserEdition();
        $request_input
            ->setUserId($request->query->get('user_id'))
            ->setUserGroupId($request->query->get('user_group_id'));
        $validator = $this->get('validator');
        $errors = $validator->validate($request_input);
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array(),
            'params' => array()
        );
        if (count($errors) == 0) {

            $sql = "
                SELECT
                    group_param.ID_GroupParam AS 'id',
                    group_param.name AS 'name',
                    user_params.value AS 'value'
                FROM
                    group_param
        
                LEFT JOIN(
                    SELECT
                        *
                    FROM
                        param_value
                    WHERE
                        param_value.ID_User = :user_id
                    ) AS user_params
                ON
                    group_param.ID_GroupParam = user_params.ID_GroupParam
        
                WHERE
                    group_param.ID_UserGroup = :user_group_id";
            $query = $this->getDoctrine()->getConnection()->prepare($sql);
            $query->execute(array(
                'user_id' => $request_input->getUserId(),
                'user_group_id' => $request_input->getUserGroupId()
                ));
            $params = $query->fetchAll();
            foreach($params as $param){
                $param_info = [];
                $param_info['name'] = $param['name'];
                $param_info['value'] = $param['value'];
                $param_info['id'] = $param['id'];
                array_push($Request_output['params'],$param_info);
            }
        }
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'],$error->getInvalidValue());
        }
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/user_insert", name = "user_insert")
     *
     * @Method("POST")
     */
    public function insertUserParams(Request $request){
        $user_id = new UserIDCheck();
        $user_id->setUserID($request->query->get('user_id'));
        $em = $this->getDoctrine()->getManager();
        /** @var  $em EntityManager */
        $validator = $this->get('validator');
        $errors = $validator->validate($user_id);
        if (count($errors) == 0){
            /** @var  $user User*/
            $user = $em->getRepository("AppBundle:User")->findBy(array('ID_User' => $user_id->getUserID()));
            $general_settings = json_decode($request->query->get('general_settings'));
            $additional_settings = json_decode($request->query->get('additional_settings'));
            $user_group = $em->getRepository("AppBundle:UserGroup")->find($general_settings['group_id']);
            $user->setUserGroup($user_group);
            $user->setEmail($general_settings['email']);
            $em->persist($user);
            $em->flush();
            foreach($additional_settings as $param){
                /** @var  $group_param GroupParam*/
                $group_param = $em->getRepository("AppBundle:GroupParam")->find($param['id']);
                /** @var  $param_value ParamValue*/
                $param_value = $em->getRepository("AppBundle:ParamValue")->findBy(array('ID_User' => $user, 'ID_GroupParam' => $group_param));
                if(!$param_value){
                    $param_value->setGroupParam($group_param);
                    $param_value->setUser($user);
                }
                $param_value->setValue($param['value']);
                $em->persist($param_value);
                $em->flush();
            }
        }
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'],$error->getInvalidValue());
        }
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/timetable", name="admin_timetable")
     */
    public function showTimetableAction(Request $request){
        return $this->render("admin/timetable.html.twig");
    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/show_day_schedule", name = "admin_show_day_schedule")
     *
     * @Method("GET")
     */
    public function showDayScheduleAction(Request $request){
        $date = $request->query->get('date');
        $sql = "
            SELECT
                sertificate.ID_Sertificate AS 'id',
                flight_type.name AS 'flight_type',
                Sertificate.use_time 
            FROM
                sertificate
            LEFT JOIN
                flight_type
            ON
                flight_type.ID_FlightType = sertificate.ID_FlightType
            WHERE
                (YEAR(Sertificate.use_time) = YEAR(FROM_UNIXTIME(:user_date))) AND
                (MONTH(Sertificate.use_time) = MONTH(FROM_UNIXTIME(:user_date))) AND
                (DAY(Sertificate.use_time) = DAY(FROM_UNIXTIME(:user_date)))
            ORDER BY
                Sertificate.use_time";
        $query = $this->getDoctrine()->getConnection()->prepare($sql);
        $query->execute(array('user_date' => $date));
        $certs = $query->fetchAll();
        $cert_list = [];
        foreach($certs AS $cert){
            $hour = date("G", strtotime($cert['use_time']));

            if (!isset($cert_list[$hour])){
                $cert_list[$hour] = [];
            }

            array_push($cert_list[$hour], $cert);
        }
        $response = new Response;
        $response->setContent(json_encode($cert_list));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/admin/cert_edit", name = "cert_edit")
     *
     * @Method("POST")
     */
    public function EditCertAction(Request $request){
        
        $cert_edition = new CertEdition();
        $cert_edition->setCertId($request->query->get('cert_id'))->setTime($request->query->get('date'));

        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );

        $validator = $this->get('validator');
        $errors = $validator->validate($cert_edition);
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'],$error->getInvalidValue());
        }
        
        if(count($errors) == 0){
            /** @var $certificate_stuff CertificateStuff */
            $cert_stuff = $this->get("app.certificate_stuff");
            $cert = $cert_stuff->CertEdition($cert_edition->getCertId(), array("use_time"), array($cert_edition->getTime()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($cert);
            $em->flush();
            array_push($Request_output, 'success');
        }

        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

}