<?php

namespace AppBundle\Controller;

use AppBundle\DataClasses\CertPaymentNotifying;
use AppBundle\Entity\Sertificate;
use AppBundle\Entity\User;
use AppBundle\Stuff\CertificateStuff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


class DealerController extends Controller
{
    /**
     * @Route("/dealer", name="dealer_index")
     */
    public function dealerIndexAction(Request $request)
    {
        //Fetching user info
        $user = $this->getUser();

        /** @var $certificate_stuff CertificateStuff */
        $certificate_stuff = $this->get("app.certificate_stuff");

        //Fetching certificates
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT s
                                   FROM AppBundle:Sertificate s
                                   WHERE (s.ID_SertState NOT IN ('1')) AND 
                                         (s.ID_User = ?1)");
        $query->setParameter(1, $user->getIDUser());
        $certificates = $certificate_stuff->getCertificatesWithActions($this->getUser());

        $certs_available = $em->createQuery("SELECT COUNT(s)
                                             FROM AppBundle:Sertificate s
                                             WHERE (s.ID_SertState IN ('1')) AND
                                                   (s.ID_User = ?1)");
        $certs_available->setParameter(1, $user->getIDUser());
        $certs_available = $certs_available->getSingleScalarResult();

        $first_blank = "";
        if ($certs_available > 0){
            $first_blank = $this->getDoctrine()->getRepository("AppBundle:Sertificate")->findOneBy(array(
                "ID_SertState" => "1",
                "ID_User" => $user->getIDUser(),
            ));
        }

        $percent = $this->get("app.user_stuff")->getCurrentUserParam("dealer_percent");

        return $this->render("dealer/index.html.twig", array(
            "user" => $user,
            "mentor_name" => $this->get("app.user_stuff")->getDisplayName($user->getIDMentor()),
            "mentor_phone" => $this->get("app.user_stuff")->getUserParam($user->getIDMentor(), "dealer_phone"),
            "certificates" => $certificates,
            "certs_available" => $certs_available,
            "first_blank" => $first_blank,
            "percent" => $percent,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/dealer/pay_select", name="dealer_pay_select")
     * 
     * @Method("GET")
     */
    public function dealerPaySelectAction(Request $request)
    {
        $user = $this->getUser();
        $ready_certs = [];
        $em = $this->getDoctrine()->getManager();
        /** @var  $em EntityManager */
        $certs = $em->getRepository("AppBundle:Sertificate")->findBy(array('ID_User' => $user, 'ID_SertState' => 4  ));
        foreach ($certs as $cert){
            /**  @var $cert Sertificate*/
            $cert_info_array = [];
            $cert_info_array['ID_Certificate'] = $cert->getIDSertificate();
            $cert_info_array['flight_type'] = $cert->getFlightType()->getName();
            $cert_info_array['name'] = $cert->getName();
            $cert_info_array['last_name'] = $cert->getLastName();
            $cert_info_array['phone_number'] = $cert->getPhoneNumber();
            $cert_info_array['flight_price'] = $cert->getFlightType()->getPrice();
            array_push($ready_certs, $cert_info_array);
        }

        $Request_output = array(
            'error_msg' => [],
            'certs' => $ready_certs
        );

        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/dealer/pay_notify", name="dealer_pay_notify")
     *
     * @Method("GET")
     */
    public function dealerPayNotifyAction(Request $request)
    {
        $user = $this->getUser();
        $cert_ids = new CertPaymentNotifying();
        $cert_ids->setCertIds(json_decode($request->request->get('cert_ids')));
        $validator = $this->get('validator');
        $errors = $validator->validate($cert_ids);
        $em = $this->getDoctrine()->getManager();
        $Request_output = array(
            'error_msg' => [],
            'certs' => [],
            'error_param' =>[]
        );
        if (count($errors) == 0){
            $certs = $em->getRepository("AppBundle:User")->findBy(array('ID_User' => $user, 'ID_Sertificate' => $cert_ids));
            foreach ($certs as $cert){
                /**  @var $cert Sertificate*/
                $cert_info_array = [];
                $cert_info_array['ID_Certificate'] = $cert->getIDSertificate();
                $cert_info_array['flight_type'] = $cert->getFlightType()->getName();
                $cert_info_array['name'] = $cert->getName();
                $cert_info_array['last_name'] = $cert->getLastName();
                $cert_info_array['phone_number'] = $cert->getPhoneNumber();
                $cert_info_array['flight_price'] = $cert->getFlightType()->getPrice();
                array_push($Request_output['certs'], $cert_info_array);
            }
        }
        foreach($errors as $error){
            array_push($Request_output['error_msg'],$error->getMessage());
            array_push($Request_output['error_param'], $error->getInvalidValue());
        }

        $response = new Response();
        $response->setContent(json_encode($Request_output));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
