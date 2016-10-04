<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sertificate")
 */
class Sertificate implements \Serializable
{
    /**
     * @ORM\Column(name="ID_Sertificate", type="integer")
     * @ORM\Id
     */
    private $ID_Sertificate;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $use_time;

    /**
     * @ORM\ManyToOne(targetEntity="FlightType")
     * @ORM\JoinColumn(name="ID_FlightType", referencedColumnName="ID_FlightType", nullable=true)
     */
    private $ID_FlightType;

    /**
     * @ORM\ManyToOne(targetEntity="SertState")
     * @ORM\JoinColumn(name="ID_SertState", referencedColumnName="ID_SertState", nullable=false)
     */
    private $ID_SertState;

    /** 
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_User", referencedColumnName="ID_User", nullable=false)
     */
    private $ID_User;

    /**
     * @param mixed $ID_Sertificate
     * @return $this
     */
    public function setIDSertificate($ID_Sertificate)
    {
        $this->ID_Sertificate = $ID_Sertificate;
        
        return $this;
    }

    /**
     * Get IDSertificate
     *
     * @return integer
     */
    public function getIDSertificate()
    {
        return $this->ID_Sertificate;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sertificate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Sertificate
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Sertificate
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phone_number = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * Set FlightType
     *
     * @param \AppBundle\Entity\FlightType $FlightType
     *
     * @return Sertificate
     */
    public function setFlightType(FlightType $FlightType = null)
    {
        $this->ID_FlightType = $FlightType;

        return $this;
    }

    /**
     * Get FlightType
     *
     * @return \AppBundle\Entity\FlightType
     */
    public function getFlightType()
    {
        return $this->ID_FlightType;
    }

    /**
     * Set SertState
     *
     * @param \AppBundle\Entity\SertState $SertState
     *
     * @return Sertificate
     */
    public function setSertState(SertState $SertState = null)
    {
        $this->ID_SertState = $SertState;

        return $this;
    }

    /**
     * Get SertState
     *
     * @return \AppBundle\Entity\SertState
     */
    public function getSertState()
    {
        return $this->ID_SertState;
    }

    /**
     * Set User
     *
     * @param \AppBundle\Entity\User $User
     *
     * @return Sertificate
     */
    public function setUser(User $User = null)
    {
        $this->ID_User = $User;

        return $this;
    }

    /**
     * Get User
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->ID_User;
    }

    public function toArray()
    {
        return array(
            "id" => $this->getIDSertificate(),
            "name" => $this->getName(),
            "last_name" => $this->getLastName(),
            "phone" => $this->getPhoneNumber(),
            "flight_type" => $this->getFlightType()->getName(),
            "sert_state" => $this->getSertState()->getName(),
            "user" => $this->getUser()->getUsername(),
        );
    }

    public function serialize()
    {
        return json_encode($this->toArray());
    }

    public function unserialize($serialized)
    {
        $arr = json_decode($serialized);
        $this->ID_Sertificate = $arr["id"];
        $this->name = $arr["name"];
        $this->last_name = $arr["last_name"];
        $this->phone_number = $arr["phone"];
        $this->ID_FlightType = $arr["flight_type"];
        $this->ID_SertState = $arr["sert_state"];
        $this->ID_User = $arr["user"];
    }

    /**
     * Set iDFlightType
     *
     * @param \AppBundle\Entity\FlightType $iDFlightType
     *
     * @return Sertificate
     */
    public function setIDFlightType(\AppBundle\Entity\FlightType $iDFlightType = null)
    {
        $this->ID_FlightType = $iDFlightType;

        return $this;
    }

    /**
     * Get iDFlightType
     *
     * @return \AppBundle\Entity\FlightType
     */
    public function getIDFlightType()
    {
        return $this->ID_FlightType;
    }

    /**
     * Set iDSertState
     *
     * @param \AppBundle\Entity\SertState $iDSertState
     *
     * @return Sertificate
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

    /**
     * Set iDUser
     *
     * @param \AppBundle\Entity\User $iDUser
     *
     * @return Sertificate
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
     * Set userDatetime
     *
     * @param \DateTime $userDatetime
     *
     * @return Sertificate
     */
    public function setUserDatetime($userDatetime)
    {
        $this->user_datetime = $userDatetime;

        return $this;
    }

    /**
     * Get userDatetime
     *
     * @return \DateTime
     */
    public function getUserDatetime()
    {
        return $this->user_datetime;
    }

    /**
     * Set clientFlightDatetime
     *
     * @param \DateTime $clientFlightDatetime
     *
     * @return Sertificate
     */
    public function setClientFlightDatetime($clientFlightDatetime)
    {
        $this->client_flight_datetime = $clientFlightDatetime;

        return $this;
    }

    /**
     * Get clientFlightDatetime
     *
     * @return \DateTime
     */
    public function getClientFlightDatetime()
    {
        return $this->client_flight_datetime;
    }

    /**
     * Set useTime
     *
     * @param \DateTime $useTime
     *
     * @return Sertificate
     */
    public function setUseTime($useTime)
    {
        $this->use_time = $useTime;

        return $this;
    }

    /**
     * Get useTime
     *
     * @return \DateTime
     */
    public function getUseTime()
    {
        return $this->use_time;
    }
}
