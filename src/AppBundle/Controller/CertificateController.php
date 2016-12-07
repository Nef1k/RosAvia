<?php

namespace AppBundle\Controller;

use AppBundle\DataClasses\CertEdition;
use AppBundle\Form\CertGroupProcessingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use AppBundle\Stuff\SMSAero\SMSAero;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Stuff;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\ParamValue;
use AppBundle\Entity\CertificateActionsHistory;
use AppBundle\Entity\SertAction;
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
            /** @var  $certificate  Sertificate*/
            $new_state_id = $this->calcCertificateState($certificate);
            $new_state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find($new_state_id);
            $certificate->setSertState($new_state);

            //Persist info into database
            $em->persist($certificate);
            $cert_action_event = new CertificateActionsHistory();
            $date = new \DateTime();
            $cert_action_event
                ->setIDSertificate($certificate)
                ->setIDUser($this->getUser())
                ->setIDSertState($certificate->getIDSertState())
                ->setEventTime($date);
            $em->persist($cert_action_event);
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
        /** @var  $certificate Sertificate*/
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
        $cert_action_event = new CertificateActionsHistory();
        $date = new \DateTime();
        $cert_action_event
            ->setIDSertificate($certificate)
            ->setIDUser($this->getUser())
            ->setIDSertState($certificate->getIDSertState())
            ->setEventTime($date);
        $em->persist($cert_action_event);
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
        /** @var  $certificate Sertificate*/
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
        $cert_action_event = new CertificateActionsHistory();
        $date = new \DateTime();
        $cert_action_event
            ->setIDSertificate($certificate)
            ->setIDUser($this->getUser())
            ->setIDSertState($certificate->getIDSertState())
            ->setEventTime($date);
        $em->persist($cert_action_event);
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
        $ids = new CertEdition();
        $ids->setCertId(json_decode($request->request->get('ids')));
        $field_names = json_decode($request->request->get('field_names'));
        $field_values = json_decode($request->request->get('field_values'));
        /** @var $certificate_stuff CertificateStuff */
        $validator = $this->get('validator');
        $errors = $validator->validate($ids);
        $em = $this->getDoctrine()->getManager();
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        if (count($errors) == 0) {
            $cert_stuff = $this->get("app.certificate_stuff");
            /** @var  $cert Sertificate[]*/
            $cert = $cert_stuff->CertEdition($ids->getCertId(), $field_names, $field_values);
            for($i = 0; $i < count($cert); $i++) {
                $em->persist($cert[$i]);
            }
            array_push($Request_output, 'success');
        }
        $em->flush();
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }
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

    /**
     *
     * @Method("GET")
     * @Route("/certificate/get_sort_params", name="certificate_get_sort_params")
     */
    public function getCertificateParamsAction(Request $request){
        /** @var  $cert_stuff CertificateStuff*/
        $cert_stuff = $this->get("app.certificate_stuff");
        $cert_state = $request->query->get("cert_state");
        $Request_output = array();
        $response = new Response();
        $Request_output["dealers"] = $cert_stuff->GetCertUserLogins($cert_state);
        $Request_output["flight_types"] = $cert_stuff->GetCertFlightTypes($cert_state);
        $response->setContent(json_encode($Request_output));
        $response -> headers -> set('Content-Type', 'application/json');
        return $response;
    }
}