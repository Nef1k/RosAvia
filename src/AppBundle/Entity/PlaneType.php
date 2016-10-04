<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
	*@ORM\Entity
	*@ORM\Table(name="plane_type")
	*/
class PlaneType
{
	/**
		*@ORM\Column(name="ID_PlaneType", type="integer")
		*@ORM\Id
		*@ORM\GeneratedValue(strategy="AUTO")
		*/
	private $ID_PlaneType;

	/**
		*@ORM\Column(type="string", length=30)
		*/
	private $name;

    /**
     * Get iDPlaneType
     *
     * @return integer
     */
    public function getIDPlaneType()
    {
        return $this->ID_PlaneType;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PlaneType
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
}
