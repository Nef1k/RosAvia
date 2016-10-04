<?php

namespace AppBundle\Stuff;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;

class SmsStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SMSAero
     */
    private $smser;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, Api $smser)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->smser = $smser;
    }

    public function getSmsStatus($id)
    {
        return $this->em->getRepository("AppBundle:SmsStatus")->find($id);
    }

    public function sendSms($phone_number, $text)
    {
        $sms = new Sms($phone_number, $text);
        $sms_cost = $this->smser->smsCost($sms);

        $sms_instance = new \AppBundle\Entity\Sms();

        $sms_instance->setMessageText($text);
        $sms_instance->setRecipient($phone_number);
        $sms_instance->setCost($sms_cost->price);
        $sms_instance->setIDInitiator($this->tokenStorage->getToken()->getUser());
        $sms_instance->setIDSmsStatus($this->getSmsStatus(1));

        $this->em->persist($sms_instance);
        $this->em->flush();

        return $this->smser->smsSend($sms);
    }
}