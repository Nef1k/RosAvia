<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="flight_type")
 */
class FlightType
{
    /**
     * @ORM\Column(name="ID_FlightType", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_FlightType;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description_link;

    /**
     * @ORM\ManyToOne(targetEntity="PlaneType")
     * @ORM\JoinColumn(name="ID_PlaneType", referencedColumnName="ID_PlaneType")
     */
    private $ID_PlaneType;

    /**
     * Get iDFlightType
     *
     * @return integer
     */
    public function getIDFlightType()
    {
        return $this->ID_FlightType;
    }

    public function getFlightType()
    {
        return $this->getIDFlightType();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FlightType
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
     * Set description
     *
     * @param string $description
     *
     * @return FlightType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return FlightType
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set PlaneType
     *
     * @param \AppBundle\Entity\PlaneType $PlaneType
     *
     * @return FlightType
     */
    public function setPlaneType(PlaneType $PlaneType = null)
    {
        $this->ID_PlaneType = $PlaneType;

        return $this;
    }

    /**
     * Get PlaneType
     *
     * @return \AppBundle\Entity\PlaneType
     */
    public function getPlaneType()
    {
        return $this->ID_PlaneType;
    }

    /**
     * Set descriptionLink
     *
     * @param string $descriptionLink
     *
     * @return FlightType
     */
    public function setDescriptionLink($descriptionLink)
    {
        $this->description_link = $descriptionLink;

        return $this;
    }

    /**
     * Get descriptionLink
     *
     * @return string
     */
    public function getDescriptionLink()
    {
        return $this->description_link;
    }

    /**
     * Set iDPlaneType
     *
     * @param \AppBundle\Entity\PlaneType $iDPlaneType
     *
     * @return FlightType
     */
    public function setIDPlaneType(\AppBundle\Entity\PlaneType $iDPlaneType = null)
    {
        $this->ID_PlaneType = $iDPlaneType;

        return $this;
    }

    /**
     * Get iDPlaneType
     *
     * @return \AppBundle\Entity\PlaneType
     */
    public function getIDPlaneType()
    {
        return $this->ID_PlaneType;
    }
}
