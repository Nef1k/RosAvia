<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 06.12.2016
 * Time: 16:45
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="certificate_actions_history")
 */
class CertificateActionsHistory
{
    /**
     * @ORM\Column(name="ID_HistoryEvent", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_HistoryEvent;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_User", referencedColumnName="ID_User", nullable=false)
     */
    private $ID_User;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $event_time;

    /**
     * @ORM\ManyToOne(targetEntity="Sertificate")
     * @ORM\JoinColumn(name="ID_Sertificate", referencedColumnName="ID_Sertificate", nullable=false)
     */
    private $ID_Sertificate;

    /**
     * @ORM\ManyToOne(targetEntity="SertState")
     * @ORM\JoinColumn(name="ID_SertState", referencedColumnName="ID_SertState", nullable=false)
     */
    private $ID_SertState;

    /**
     * Get iDHistoryEvent
     *
     * @return integer
     */
    public function getIDHistoryEvent()
    {
        return $this->ID_HistoryEvent;
    }

    /**
     * Set eventTime
     *
     * @param \DateTime $eventTime
     *
     * @return CertificateActionsHistory
     */
    public function setEventTime($eventTime)
    {
        $this->event_time = $eventTime;

        return $this;
    }

    /**
     * Get eventTime
     *
     * @return \DateTime
     */
    public function getEventTime()
    {
        return $this->event_time;
    }

    /**
     * Set iDUser
     *
     * @param \AppBundle\Entity\User $iDUser
     *
     * @return CertificateActionsHistory
     */
    public function setIDUser(\AppBundle\Entity\User $iDUser)
    {
        $this->ID_User = $iDUser;

        return $this;
    }

    /**
     * Get iDUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getIDUser()
    {
        return $this->ID_User;
    }

    /**
     * Set iDSertificate
     *
     * @param \AppBundle\Entity\Sertificate $iDSertificate
     *
     * @return CertificateActionsHistory
     */
    public function setIDSertificate(\AppBundle\Entity\Sertificate $iDSertificate)
    {
        $this->ID_Sertificate = $iDSertificate;

        return $this;
    }

    /**
     * Get iDSertificate
     *
     * @return \AppBundle\Entity\Sertificate
     */
    public function getIDSertificate()
    {
        return $this->ID_Sertificate;
    }

    /**
     * Set iDSertState
     *
     * @param \AppBundle\Entity\SertState $iDSertState
     *
     * @return CertificateActionsHistory
     */
    public function setIDSertState(\AppBundle\Entity\SertState $iDSertState)
    {
        $this->ID_SertState = $iDSertState;

        return $this;
    }

    /**
     * Get iDSertState
     *
     * @return \AppBundle\Entity\SertState
     */
    public function getIDSertState()
    {
        return $this->ID_SertState;
    }
}
