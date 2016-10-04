<?php

namespace AppBundle\Entity;

use AppBundle\Entity\PlaneChar;
use AppBundle\Entity\PlaneType;
use Doctrine\ORM\Mapping as ORM;

/**
  *@ORM\Entity
  *@ORM\Table(name="planetype_planechar")
  */
class planetype_planechar
{
  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="PlaneType")
   * @ORM\JoinColumn(name="ID_PlaneType", referencedColumnName="ID_PlaneType")
   */
  private $ID_PlaneType;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="PlaneChar")
   * @ORM\JoinColumn(name="ID_PlaneChar", referencedColumnName="ID_PlaneChar")
   */
  private $ID_PlaneChar;

  /**
    * @ORM\Column(type="string", length=30)
    */
  private $value;

    public function __construct(PlaneType $IDPlaneType, PlaneChar $IDPlaneChar, $value = "")
    {
        $this->setIDPlaneChar($IDPlaneChar);
        $this->setIDPlaneType($IDPlaneType);
        $this->setValue($value);
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return planetype_planechar
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set iDPlaneType
     *
     * @param PlaneType $iDPlaneType
     *
     * @return planetype_planechar
     */
    public function setIDPlaneType(PlaneType $iDPlaneType)
    {
        $this->ID_PlaneType = $iDPlaneType;

        return $this;
    }

    /**
     * Get iDPlaneType
     *
     * @return PlaneType
     */
    public function getIDPlaneType()
    {
        return $this->ID_PlaneType;
    }

    /**
     * Set iDPlaneChar
     *
     * @param PlaneChar $iDPlaneChar
     *
     * @return planetype_planechar
     */
    public function setIDPlaneChar(PlaneChar $iDPlaneChar)
    {
        $this->ID_PlaneChar = $iDPlaneChar;

        return $this;
    }

    /**
     * Get iDPlaneChar
     *
     * @return PlaneChar
     */
    public function getIDPlaneChar()
    {
        return $this->ID_PlaneChar;
    }
}
