<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SertAction;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\SertState;
use AppBundle\Form\CertGroupProcessing;
use AppBundle\Form\CertGroupProcessingType;
use AppBundle\Stuff\CertificateStuff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CertificateCheckType;

class ManagerController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/manager", name="managerIndex")
     */
    public function managerIndexAction(Request $request)
    {
        $user = $this->getUser();
        $dealer_list = [];//$this->get("app.manager_stuff")->getDealerTable();

        $query_sql = "SELECT
            sertificate_state.ID_SertState,
            sertificate_state.name,
            COUNT(sertificates.ID_SertState) AS `count`
        FROM
            sertificate_state
        
        LEFT OUTER JOIN
        (
            SELECT
                ID_Sertificate,
                ID_SertState
            FROM
                sertificate
            WHERE
                ID_User IN (
                    SELECT 
                        ID_User
                    FROM
                        user
                    WHERE
                        user.ID_Mentor = :manager_id
                )
        ) AS sertificates
        ON
            sertificates.ID_SertState = sertificate_state.ID_SertState
        
        GROUP BY
            sertificate_state.name
        ORDER BY
            sertificate_state.ID_SertState";
        $query = $this->getDoctrine()->getConnection()->prepare($query_sql);
        $query->execute(array(
            "manager_id" => $user->getIDUser(),
        ));
        $certificate_states = $query->fetchAll();

        return $this->render("manager/index.html.twig", array(
            "certificate_states" => $certificate_states,
            "user" => $user,
            "dealer_list" => $dealer_list,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/manager/view_certificates/{state_id}", name="view_certificates")
     */
    public function viewCertificatesAction($state_id, Request $request)
    {
        //$state = $this->getDoctrine()->getRepository("AppBundle:SertState")->find($state_id);
        /**
         * @var $certificate_stuff CertificateStuff
         */
        $certificate_stuff = $this->get("app.certificate_stuff");
        $certificates = $certificate_stuff->getCertificatesByMentor($this->getUser()->getIDUser(), $state_id);
        $grouped_certificates = $certificate_stuff->groupCertificatesBy($certificates, function($certificate){
            /** @var $certificate Sertificate */
            return $certificate->getUser()->getUsername();
        });

        //Paste from sublime here
        $action_form = $this->createForm(CertGroupProcessingType::class, array(
            "certificates" => $certificates,
            "state_id" => $state_id,
        ));

        $action_form->handleRequest($request);
        if ($action_form->isSubmitted() && $action_form->isValid()){
            $certificate_stuff->groupProcessCertificates($action_form->getData());
            return $this->redirectToRoute("homepage");
        }
        
        return $this->render("manager/view_certificates.html.twig", array(
            "dealers" => $grouped_certificates,
            "action_form" => $action_form->createView(),
        ));
    }
}