<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Sms
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sms")
 */
class Sms{
    /**
     * @ORM\Column(name="ID_Sms", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_Sms;

    /**
     * @ORM\Column(name="recipient", type="string", length=20)
     */
    private $recipient;

    /**
     * @ORM\Column(name="message_text", type="text")
     */
    private $message_text;

    /**
     * @ORM\Column(name="cost", type="decimal", precision=6, scale=2)
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity="SmsStatus")
     * @ORM\JoinColumn(name="ID_SmsStatus", referencedColumnName="ID_SmsStatus")
     */
    private $ID_SmsStatus;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_Initiator", referencedColumnName="ID_User")
     */
    private $ID_Initiator;

    /**
     * Get iDSms
     *
     * @return integer
     */
    public function getIDSms()
    {
        return $this->ID_Sms;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return Sms
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set messageText
     *
     * @param string $messageText
     *
     * @return Sms
     */
    public function setMessageText($messageText)
    {
        $this->message_text = $messageText;

        return $this;
    }

    /**
     * Get messageText
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->message_text;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return Sms
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set iDSmsStatus
     *
     * @param \AppBundle\Entity\SmsStatus $iDSmsStatus
     *
     * @return Sms
     */
    public function setIDSmsStatus(\AppBundle\Entity\SmsStatus $iDSmsStatus = null)
    {
        $this->ID_SmsStatus = $iDSmsStatus;

        return $this;
    }

    /**
     * Get iDSmsStatus
     *
     * @return \AppBundle\Entity\SmsStatus
     */
    public function getIDSmsStatus()
    {
        return $this->ID_SmsStatus;
    }

    /**
     * Set iDInitiator
     *
     * @param \AppBundle\Entity\User $iDInitiator
     *
     * @return Sms
     */
    public function setIDInitiator(\AppBundle\Entity\User $iDInitiator = null)
    {
        $this->ID_Initiator = $iDInitiator;

        return $this;
    }

    /**
     * Get iDInitiator
     *
     * @return \AppBundle\Entity\User
     */
    public function getIDInitiator()
    {
        return $this->ID_Initiator;
    }
}
