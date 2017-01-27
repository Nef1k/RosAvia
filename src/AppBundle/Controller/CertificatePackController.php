<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 14:33
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CertificatePack;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\User;
use AppBundle\Stuff\UserStuff;
use AppBundle\Stuff\CertificatePackStuff;
use AppBundle\Stuff\CertificateStuff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CertificatePackController extends Controller{

    /**
     * @param Request $request
     * @Route("/certificate_pack/create", name="certificate_pack_create")
     * @Method("POST")
     * @return Response
     */
    public function createCertificatePackAction(Request $request){
        $current_user = $this->getUser();
        $certificate_ids_in_pack = json_decode($request->request->get("cert_ids"));
        $payment_method_id = $request->request->get("payment_method");
        /** @var  $certificate_pack_stuff CertificatePackStuff*/
        $certificate_pack_stuff = $this->get("app.certificate_pack_stuff");
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        $Request_output['pack_id'] = $certificate_pack_stuff->createCertificatePack($current_user, $certificate_ids_in_pack, $payment_method_id);
        /**foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }**/
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @return Response
     * @Route("/certificate_pack/get_payment_methods", name="get_payment_methods")
     * @Method("GET")
     */
    public function selectPaymentMethodsAction(){
        $em = $this->getDoctrine()->getManager();
        /** @var  $payment_method_list PaymentMethod[]*/
        $payment_method_list = $em->getRepository("AppBundle:PaymentMethod")->findBy([]);
        $payment_methods_info = array();
        /** @var  $payment_method PaymentMethod*/
        foreach($payment_method_list AS $payment_method){
            $payment_method_info = array();
            $payment_method_info['id'] = $payment_method->getIDPaymentMethod();
            $payment_method_info['name'] = $payment_method->getPaymentMethodName();
            array_push($payment_methods_info, $payment_method_info);
        }
        $response = new Response();
        $response->setContent(json_encode($payment_methods_info));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @Route("/certificate_pack/action", name="certificate_pack_action")
     * @Method("POST")
     * @return Response
     */
    public function createActionAtCertificatePacksAction(Request $request) {
        $certificate_pack_ids = json_decode($request->request->get("pack_id"));
        $is_pack_activated = $request->request->get("is_activated");
        /** @var  $certificate_pack_stuff CertificatePackStuff*/
        $certificate_pack_stuff = $this->get("app.certificate_pack_stuff");
        $certificate_pack_stuff->createActionWithCertificatePacks($certificate_pack_ids, $is_pack_activated);
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        /**foreach($errors as $error){
        array_push($Request_output['error_msg'],$error->getMessage());
        array_push($Request_output['error_param'], $error->getInvalidValue());
        }**/
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @Route("/certificate_pack/select", name="certificate_select")
     * @Method("POST")
     * @return Response
     */
    public function getCertificatePacksAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $fields = json_decode($request->request->get('field_names'));
        /** @var  $all_certificate_packs CertificatePack[]*/
        $all_certificate_packs = $em->getRepository("AppBundle:CertificatePack")->findBy(array(),array("ID_User" => 'ASC'));
        /** @var  $certificate_pack CertificatePack*/
        /** @var  $certificate_stuff CertificateStuff*/
        $certificate_stuff = $this->get('app.certificate_stuff');
        $Request_output = array();
        foreach($all_certificate_packs AS $certificate_pack){
            $certificate_pack_info = array();
            $cert_state_waiting_activation = $em->getRepository("AppBundle:SertState")->find(4);
            $cerificates_in_pack_list = $certificate_stuff->GetCertArray(array(
                'ID_CertificatePack' => $certificate_pack,
                'ID_SertState' => $cert_state_waiting_activation
            ), array(
                'ID_User' => 'ASC',
                'ID_CertificatePack' => 'DESC'
            ), $fields);
            $user_id = $certificate_pack->getIDUser()->getIDUser();
            /** @var  $user_stuff UserStuff*/
            $user_stuff = $this->get("app.user_stuff");
            /** @var  $user User*/
            $user = $em->getRepository("AppBundle:User")->find($user_id);
            $percent = $user_stuff->getUserParam($user, "dealer_percent");
            $certificate_pack_info['user_login'] = $user->getUsername();
            $certificate_pack_info['percent'] = $percent;
            $certificate_pack_info['pack_id'] = $certificate_pack->getIDCertificatePack();
            $certificate_pack_info['pack_payment_method'] = $certificate_pack->getIDPaymentMethod()->getPaymentMethodName();
            $certificate_pack_info['certificates'] = $cerificates_in_pack_list;
            array_push($Request_output, $certificate_pack_info);
        }
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }
}