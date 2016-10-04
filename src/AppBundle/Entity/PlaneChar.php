<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
	*@ORM\Entity
	*@ORM\Table(name="plane_char")
	*/
class PlaneChar
{
	/**
		*@ORM\Column(name="ID_PlaneChar", type="integer")
		*@ORM\Id
		*@ORM\GeneratedValue(strategy="AUTO")
		*/
	private $ID_PlaneChar;

	/**
		*@ORM\Column(type="string", length=50)
		*/
	private $name;

    /**
     * Get iDPlaneChar
     *
     * @return integer
     */
    public function getIDPlaneChar()
    {
        return $this->ID_PlaneChar;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PlaneChar
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
