<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="param_value")
 */
class ParamValue
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_User", referencedColumnName="ID_User")
     */
    private $ID_User;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="GroupParam")
     * @ORM\JoinColumn(name="ID_GroupParam", referencedColumnName="ID_GroupParam")
     */
    private $ID_GroupParam;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $value;

    public function __construct(User $user, GroupParam $groupParam, $value = "")
    {
        $this->setUser($user);
        $this->setGroupParam($groupParam);
        $this->setValue($value);
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ParamValue
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
     * Set User
     *
     * @param \AppBundle\Entity\User $User
     *
     * @return ParamValue
     */
    public function setUser(User $User)
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

    /**
     * Set iDGroupParam
     *
     * @param \AppBundle\Entity\GroupParam $GroupParam
     *
     * @return ParamValue
     */
    public function setGroupParam(GroupParam $GroupParam)
    {
        $this->ID_GroupParam = $GroupParam;

        return $this;
    }

    /**
     * Get iDGroupParam
     *
     * @return \AppBundle\Entity\GroupParam
     */
    public function getGroupParam()
    {
        return $this->ID_GroupParam;
    }

    /**
     * Set iDUser
     *
     * @param \AppBundle\Entity\User $iDUser
     *
     * @return ParamValue
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
     * Set iDGroupParam
     *
     * @param \AppBundle\Entity\GroupParam $iDGroupParam
     *
     * @return ParamValue
     */
    public function setIDGroupParam(\AppBundle\Entity\GroupParam $iDGroupParam)
    {
        $this->ID_GroupParam = $iDGroupParam;

        return $this;
    }

    /**
     * Get iDGroupParam
     *
     * @return \AppBundle\Entity\GroupParam
     */
    public function getIDGroupParam()
    {
        return $this->ID_GroupParam;
    }
}
