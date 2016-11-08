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
}