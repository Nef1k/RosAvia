<?php

namespace AppBundle\Entity;

use AppBundle\Entity\UserGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="group_param")
 */
class GroupParam
{
    /**
     * @ORM\Column(name="ID_GroupParam", type="string", length=50)
     * @ORM\Id
     */
    private $ID_GroupParam;

    /**
     * @ORM\ManyToOne(targetEntity="UserGroup")
     * @ORM\JoinColumn(name="ID_UserGroup", referencedColumnName="ID_UserGroup")
     */
    private $ID_UserGroup;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * Get iDGroupParam
     *
     * @return integer
     */
    public function getIDGroupParam()
    {
        return $this->ID_GroupParam;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return GroupParam
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
     * Set iDUserGroup
     *
     * @param \AppBundle\Entity\UserGroup $UserGroup
     *
     * @return GroupParam
     */
    public function setUserGroup(UserGroup $UserGroup = null)
    {
        $this->ID_UserGroup = $UserGroup;

        return $this;
    }

    /**
     * Get iDUserGroup
     *
     * @return \AppBundle\Entity\UserGroup
     */
    public function getUserGroup()
    {
        return $this->ID_UserGroup;
    }
}
