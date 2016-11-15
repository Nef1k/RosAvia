<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 14:33
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CertificatePack;
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
     * @param Request $request
     * @Route("/certificate_pack/action", name="certificate_pack_action")
     * @Method("POST")
     * @return Response
     */
    public function createActionAtCertificatePacksAction(Request $request) {
        $certificate_pack_ids = $request->request->get("pack_id");
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
        $all_certificate_packs = $em->getRepository("AppBundle:CertificatePack")->findAll();
        /** @var  $certificate_pack CertificatePack*/
        /** @var  $certificate_stuff CertificateStuff*/
        $certificate_stuff = $this->get('app.certificate_stuff');
        $Request_output = array();
        foreach($all_certificate_packs AS $certificate_pack){
            $certificate_pack_info = array();
            $cerificates_in_pack_list = $certificate_stuff->GetCertArray(array(
                'ID_CertificatePack' =>$certificate_pack
            ), array(
                'ID_User' => 'ASC',
                'ID_CertificatePack' => 'DESC'
            ), $fields);
            $certificate_pack_info['user'] = $certificate_pack->getIDUser();
            $certificate_pack_info['pack_id'] = $certificate_pack->getIDCertificatePack();
            $certificate_pack_info['certificates'] = $cerificates_in_pack_list;
            array_push($Request_output, $certificate_pack_info);
        }
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }
}