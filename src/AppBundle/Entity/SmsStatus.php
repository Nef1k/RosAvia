<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class SmsStatus
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sms_status")
 */
class SmsStatus{
    /**
     * @ORM\Column(name="ID_SmsStatus", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_SmsStatus;

    /**
     * @ORM\Column(name="status_name", type="string", length=100)
     */
    private $status_name;

    /**
     * @ORM\Column(name="status_text", type="text")
     */
    private $status_text;

    /**
     * Get iDSmsStatus
     *
     * @return integer
     */
    public function getIDSmsStatus()
    {
        return $this->ID_SmsStatus;
    }

    /**
     * Set statusName
     *
     * @param string $statusName
     *
     * @return SmsStatus
     */
    public function setStatusName($statusName)
    {
        $this->status_name = $statusName;

        return $this;
    }

    /**
     * Get statusName
     *
     * @return string
     */
    public function getStatusName()
    {
        return $this->status_name;
    }

    /**
     * Set statusText
     *
     * @param string $statusText
     *
     * @return SmsStatus
     */
    public function setStatusText($statusText)
    {
        $this->status_text = $statusText;

        return $this;
    }

    /**
     * Get statusText
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->status_text;
    }
}
