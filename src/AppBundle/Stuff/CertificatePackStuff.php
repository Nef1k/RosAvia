<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 15:34
 */

namespace AppBundle\Stuff;

use AppBundle\Entity\CertificatePack;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class CertificatePackStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CertificateStuff
     */
    private $certificate_stuff;

    /**
     * @var TokenStorage
     */
    private $tokens;

    /**
     * CertificatePackStuff constructor.
     * @param EntityManager $em
     * @param CertificateStuff $certificateStuff
     */
    public function __construct(EntityManager $em, CertificateStuff $certificateStuff, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->certificate_stuff = $certificateStuff;
        $this->tokens = $tokenStorage;
    }

    /**
     * @param $current_user
     * @param $cert_ids_in_pack
     * @param $payment_method_id
     */
    public function createCertificatePack($current_user, $cert_ids_in_pack, $payment_method_id){
        /** @var  $certificatePack CertificatePack*/
        $certificatePack = new CertificatePack();
        /** @var  $payment_method PaymentMethod*/
        $payment_method = $this->em->getRepository("AppBundle:PaymentMethod")->find($payment_method_id);
        $certificatePack->
            setIDUser($current_user)->
            setIDPaymentMethod($payment_method)->
            setCount(count($cert_ids_in_pack))->
            setCreationDate(new \DateTime());
        $this->em->persist($certificatePack);
        foreach($cert_ids_in_pack AS $cert_id_in_pack){
            /** @var  $certificate_in_pack Sertificate*/
            $certificate_in_pack = $this->em->getRepository("AppBundle:Sertificate")->find($cert_id_in_pack);
            $certificate_in_pack->setIDCertificatePack($certificatePack);
            $this->em->persist($certificate_in_pack);
        }
        $this->em->flush();
    }

    /**
     * @param $certificate_pack_id
     * @param $is_pack_activated
     */
    public function createActionWithCertificatePack($certificate_pack_id, $is_pack_activated){
        /** @var  $certificates_in_pack Sertificate[]*/
        $certificates_in_pack = $this->em->getRepository("AppBundle:Sertificate")->findBy(array("ID_CertificatePack" => $certificate_pack_id));
        /** @var  $certificate_in_pack Sertificate*/
        foreach($certificates_in_pack AS $certificate_in_pack){
            $certificate_in_pack->setIDCertificatePack();
            $this->em->persist($certificate_in_pack);
            if ($is_pack_activated) {
                $this->certificate_stuff->activateCertificates($certificates_in_pack);
            }
        }
        $certificate_pack = $this->em->getRepository("AppBundle:CertificatePack")->find($certificate_pack_id);
        $this->em->remove($certificate_pack);
        $this->em->flush();
    }

    /**
     * @param $certificate_pack_ids
     * @param $is_packs_activated
     */
    public function createActionWithCertificatePacks($certificate_pack_ids, $is_packs_activated) {
        foreach($certificate_pack_ids AS $certificate_pack_id){
            $this->createActionWithCertificatePack($certificate_pack_id, $is_packs_activated);
        }
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
        if ((isset($object["phone_number"])?$object["phone_number"]:null) != null) $criteria["phone_number"] = $object["phone_number"];
        if ((isset($object["use_time"])?$object["use_time"]:null) != null) $criteria["use_time"] = strtotime($object["use_time"]);
        if ((isset($object["ID_FlightType"])?$object["ID_FlightType"]:null) != null) $criteria["ID_FlightType"] = $this->em->getRepository("AppBundle:FlightType")->findBy(array("ID_FlightType" => $object["ID_FlightType"]));
        if ((isset($object["ID_SertState"])?$object["ID_SertState"]:null) != null) $criteria["ID_SertState"] = $this->em->getRepository("AppBundle:SertState")->findBy(array("ID_SertState" => $object["ID_SertState"]));
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

}