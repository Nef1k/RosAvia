<?php

namespace AppBundle\Controller;

use AppBundle\Form\CertGroupProcessingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use AppBundle\Stuff\SMSAero\SMSAero;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Stuff;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\ParamValue;
use AppBundle\Form\CertificateEditType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Stuff\CertificateStuff;

class CertificateController extends Controller
{
    public static function calcCertificateState(Sertificate $certificate)
    {
        if ($certificate->getName() &&
            $certificate->getLastName() &&
            $certificate->getPhoneNumber()&&
            $certificate->getFlightType()){
            return 3;
        } else if (!($certificate->getName() ||
                     $certificate->getLastName() ||
                     $certificate->getPhoneNumber() ||
                     $certificate->getFlightType())){
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * @param $cert_id
     * @param Request $request
     * @return Response
     *
     * @Route("/certificate/edition/{cert_id}", name="edition")
     */
    public function editCertAction($cert_id, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository("AppBundle:Sertificate");
        $em = $this->getDoctrine()->getManager();

        //Fetching certificate
        $certificate = $repo->find($cert_id);
        if (!$certificate){
            $this->createNotFoundException("Invalid certificate ID \"".$cert_id."\"");
        }
        $this->denyAccessUnlessGranted("certificateEdit", $certificate);

        //Fetching user
        $user = $this->getUser();

        //Form stuff
        $form = $this->createForm(CertificateEditType::class, $certificate);
        $form->handleRequest($request);

        //If form was posted here and it is valid
        if ($form->isSubmitted() && $form->isValid()){
            //Update certificate state
            $new_state_id = $this->calcCertificateState($certificate);
            $new_state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find($new_state_id);
            $certificate->setSertState($new_state);

            //Persist info into database
            $em->persist($certificate);
            $em->flush();

            //Going back to homepage
            return $this->redirectToRoute("homepage");
        }

        return $this->render("certificate/certificate_edit.html.twig", array(
            "form" => $form->createView(),
        ));
    }

    /**
     * @param $cert_id
     * @param Request $request
     * @return Response
     *
     * @Route("/certificate/activ_request/{cert_id}", name="activ_request")
     */
    public function activeRequestCertAction($cert_id, Request $request)
    {
        //Init
        $repo = $this->getDoctrine()->getRepository("AppBundle:Sertificate");
        $em = $this->getDoctrine()->getManager();

        //Fetching user
        $user = $this->getUser();

        //Fetching certificate
        $certificate = $repo->find($cert_id);
        if (!$certificate){
            $this->createNotFoundException("Invalid certificate ID \"".$cert_id."\"");
        }

        $this->denyAccessUnlessGranted("certificateActivReq", $certificate);

        //Setting up new fields
        $new_state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find(4);
        $certificate->setSertState($new_state);

        //Applying changes
        $em->persist($certificate);
        $em->flush();

        //$sms = new SMSAero();

        //$to_phone = $this->getDoctrine()->getRepository("AppBundle:ParamValue")->
        //$sms->send($certificate->getUser()->)

        return $this->redirectToRoute("homepage");
    }

    /**
     * @param $cert_id
     * @param Request $request
     * @return Response
     *
     * @Route("/certificate/clear/{cert_id}", name="clear")
     */
    public function clearAction($cert_id, Request $request)
    {
        //Init
        $repo = $this->getDoctrine()->getRepository("AppBundle:Sertificate");
        $em = $this->getDoctrine()->getManager();

        //Fetching user
        $user = $this->getUser();

        //Fetching certificate
        $certificate = $repo->find($cert_id);
        if (!$certificate){
            $this->createNotFoundException("Invalid certificate ID \"".$cert_id."\"");
        }

        $this->denyAccessUnlessGranted("certificateClear", $certificate);

        //Setting up new fields
        $certificate->setName("");
        $certificate->setLastName("");
        $certificate->setPhoneNumber("");
        $new_state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find(1);
        $certificate->setSertState($new_state);

        $new_flight = $this->getDoctrine()->getRepository("AppBundle:FlightType")->find(1);
        $certificate->setFlightType($new_flight);

        //Applying changes
        $em->persist($certificate);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("certificates/", name="certificates_group_processing")
     * @Method("POST")
     */
    public function groupProcessingAction(Request $request)
    {
        $processing_form = $this->createForm(CertGroupProcessingType::class);
        dump($processing_form);
        $processing_form->handleRequest($request);

        if ($processing_form->isSubmitted()){
            /** @var $certificate_stuff CertificateStuff */
            $certificate_stuff = $this->get("app.certificate_stuff");
            $certificate_stuff->groupProcessCertificates($processing_form->getData());
        }

        return new Response("<html><head><title>Stuff</title><body>To be redirected</body></html>");//$this->redirectToRoute("homepage");
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/certificate/select", name = "select")
     * @Method("POST")
     */
    public function certSelect(Request $request){
        $response = new Response();
        /** @var $certificate_stuff CertificateStuff */
        $cert_stuff = $this->get("app.certificate_stuff");
        $response->setContent(json_encode($cert_stuff->GetCertArrayFromRequest($request)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/certificate/edit", name = "edit")
     * @Method("POST")
     */
    public function certEdit(Request $request){
        $id = $request->request->get('id');
        $field_names = json_decode($request->request->get('field_names'));
        $field_values = json_decode($request->request->get('field_values'));
        /** @var $certificate_stuff CertificateStuff */
        $cert_stuff = $this->get("app.certificate_stuff");
        $cert = $cert_stuff->CertEdition($id, $field_names, $field_values);
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($cert);
        $em->flush();
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        array_push($Request_output, 'success');
        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Sertificate $certificate
     * @param Request $request
     * @return Response
     *
     * @Route("/certificate/view/{certificate}", name="certificate_view")
     */
    public function viewCertificate(Sertificate $certificate, Request $request){
        return $this->render("certificate/certificate_view.html.twig", [
            "certificate" => $certificate
        ]);
    }
}