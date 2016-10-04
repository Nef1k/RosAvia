<?php

namespace AppBundle\Controller;

use AppBundle\Stuff\SmsStuff;
use Zelenin\SmsRu\Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zelenin\SmsRu\Entity\Sms;

class TmpController extends Controller
{
    /**
     * @Route("/comingsoon", name="coming_soon")
     */
    public function comingSoonAction(Request $request)
    {
        return new Response("<html><head><title>This page is under construction</title></head><body><h1>Coming soon!</h1></body></html>");
    }

    /**
     * @Route("/user", name="user_panel")
     */
    public function userPanelAction(Request $request)
    {
        return $this->redirectToRoute("coming_soon");
    }

    /**
     * @Route("/edit_request", name="edit_request")
     */
    public function editRequestAction(Request $request)
    {
        return $this->redirectToRoute("coming_soon");
    }

    /**
     * @param Request $request
     *
     * @Route("/sms_test", name="sms_test")
     */
    public function smsTestAction(Request $request)
    {
        /** @var $smser SmsStuff */
        $smser = $this->get("app.sms_stuff");

        $number = $request->query->get("number");
        $text = $request->query->get("text");

        $smser->sendSms($number, $text);

        return new Response("<html><head><title>Test page</title></head><body></body></html>");
    }
}