<?php

namespace AppBundle\Entity;

use AppBundle\Entity\UserGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Этот e-mail уже занят")
 * @UniqueEntity(fields="username", message="Это имя пользователя уже занято")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     *@ORM\Column(name="ID_User", type="integer")
     *@ORM\Id
     *@ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_User;

    /**
     * @ORM\Column(name="username", type="string", length=32)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $is_active;

    /**
     * @ORM\ManyToOne(targetEntity="UserGroup")
     * @ORM\JoinColumn(name="ID_UserGroup", referencedColumnName="ID_UserGroup")
     */
    private $ID_UserGroup;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_Mentor", referencedColumnName="ID_User")
     */
    private $ID_Mentor;

    public function __construct()
    {
        $this->setIsEnabled(true);
    }

    /**
     * Get id of user
     *
     * @return integer
     */
    public function getIDUser()
    {
        return $this->ID_User;
    }

    /**
     * Set login
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

        /**
     * Get login
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array($this->getUserGroup()->getName());
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * Set UserGroup
     *
     * @param \AppBundle\Entity\UserGroup $UserGroup
     *
     * @return User
     */
    public function setUserGroup(UserGroup $UserGroup = null)
    {
        $this->ID_UserGroup = $UserGroup;

        return $this;
    }

    /** Get UserGroup
     *
     * @return \AppBundle\Entity\UserGroup
     */
    public function getUserGroup()
    {
        return $this->ID_UserGroup;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->is_active;
    }

    /**
     * @param mixed $is_active
     */
    public function setIsEnabled($is_active)
    {
        $this->is_active = $is_active;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function toArray()
    {
        return array(
            "id" => $this->getIDUser(),
            "username" => $this->getUsername(),
            "email" => $this->getEmail(),
        );
    }

    public function serialize()
    {
        return serialize(array(
            $this->getIDUser(),
            $this->getUsername(),
            $this->getPassword(),
            $this->isEnabled(),
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->ID_User,
            $this->username,
            $this->password,
            $this->is_active,
        ) = unserialize($serialized);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set iDUserGroup
     *
     * @param \AppBundle\Entity\UserGroup $iDUserGroup
     *
     * @return User
     */
    public function setIDUserGroup(\AppBundle\Entity\UserGroup $iDUserGroup = null)
    {
        $this->ID_UserGroup = $iDUserGroup;

        return $this;
    }

    /**
     * Get iDUserGroup
     *
     * @return \AppBundle\Entity\UserGroup
     */
    public function getIDUserGroup()
    {
        return $this->ID_UserGroup;
    }

    /**
     * Set iDMentor
     *
     * @param \AppBundle\Entity\User $iDMentor
     *
     * @return User
     */
    public function setIDMentor(User $iDMentor = null)
    {
        $this->ID_Mentor = $iDMentor;

        return $this;
    }

    /**
     * Get iDMentor
     *
     * @return \AppBundle\Entity\User
     */
    public function getIDMentor()
    {
        return $this->ID_Mentor;
    }
}
