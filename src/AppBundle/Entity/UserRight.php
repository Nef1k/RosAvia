<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
	*@ORM\Entity
	*@ORM\Table(name="user_right")
	*/
class UserRight
{
	/**
        *@ORM\Column(name="ID_UserRight", type="integer")
		*@ORM\Id
		*@ORM\GeneratedValue(strategy="AUTO")
		*/
	private $ID_UserRight;

	/**
		*@ORM\Column(type="string", length=30)
		*/
	private $short_name;

    /**
        *@ORM\Column(type="text")
        */
    private $description;

    /**
     * Get UserRight
     *
     * @return integer
     */
    public function getIDUserRight()
    {
        return $this->ID_UserRight;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return UserRight
     */
    public function setShortName($shortName)
    {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return UserRight
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
}
