<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 14:33
 */

namespace AppBundle\Controller;

use AppBundle\Stuff\CertificatePackStuff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CertificatePackController extends Controller
{

    /**
     * @param Request $request
     * @Route("/certificate_pack/create", name="certificate_pack_create")
     * @Method("POST")
     */
    public function createCertificatePackAction(Request $request){
        $current_user = $this->getUser();
        $certificate_ids_in_pack = json_decode($request->request->get("cert_ids"));
        $payment_method_id = $request->request->get("payment_method");
        /** @var  $certificate_pack_stuff CertificatePackStuff*/
        $certificate_pack_stuff = $this->get("certificate_pack_stuff");
        $certificate_pack_stuff->createCertificatePack($current_user, $certificate_ids_in_pack, $payment_method_id);
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }
}