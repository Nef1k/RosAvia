<?php

namespace AppBundle\Entity;

use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserRight;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="usergroup_userright")
 */
class usergroup_userright
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="UserGroup")
     * @ORM\JoinColumn(name="ID_UserGroup", referencedColumnName="ID_UserGroup")
     */
    private $ID_UserGroup;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="UserRight")
     * @ORM\JoinColumn(name="ID_UserRight", referencedColumnName="ID_UserRight")
     */
    private $ID_UserRight;

    public function __construct(UserGroup $IDUserGroup, UserRight $IDUserRight)
    {
        $this->setIDUserGroup($IDUserGroup);
        $this->setIDUserRight($IDUserRight);
    }

    /**
     * Set iDUserGroup
     *
     * @param UserGroup $iDUserGroup
     *
     * @return usergroup_userright
     */
    public function setIDUserGroup(UserGroup $iDUserGroup)
    {
        $this->ID_UserGroup = $iDUserGroup;

        return $this;
    }

    /**
     * Get iDUserGroup
     *
     * @return UserGroup
     */
    public function getIDUserGroup()
    {
        return $this->ID_UserGroup;
    }

    /**
     * Set iDUserRight
     *
     * @param UserRight $iDUserRight
     *
     * @return usergroup_userright
     */
    public function setIDUserRight(UserRight $iDUserRight)
    {
        $this->ID_UserRight = $iDUserRight;

        return $this;
    }

    /**
     * Get iDUserRight
     *
     * @return UserRight
     */
    public function getIDUserRight()
    {
        return $this->ID_UserRight;
    }
}
