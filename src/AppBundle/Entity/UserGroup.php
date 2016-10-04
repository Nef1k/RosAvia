<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\SertActions;

/**
  *@ORM\Entity
  *@ORM\Table(name="user_group")
  */
class UserGroup
{
    /**
      *@ORM\Column(name="ID_UserGroup", type="integer")
      *@ORM\Id
      *@ORM\GeneratedValue(strategy="AUTO")
      */
    private $ID_UserGroup;

    /**
      *@ORM\Column(type="string", length=50)
      */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $display_name;

    /**
     * @ORM\ManyToMany(targetEntity="SertAction", mappedBy="allowed_groups")
     */
    private $allowed_actions;

    /**
     * @ORM\Column(name="can_edit_others", type="boolean")
     */
    private $can_edit_others;

    public function __get($a)
    {
        return $this->ID_UserGroup;
    }

    /**
     * Get iDUserGroup
     *
     * @return integer
     */
    public function getIDUserGroup()
    {
        return $this->ID_UserGroup;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return UserGroup
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
     * Set url
     *
     * @param string $url
     *
     * @return UserGroup
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allowed_actions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add allowedAction
     *
     * @param \AppBundle\Entity\SertAction $allowedAction
     *
     * @return UserGroup
     */
    public function addAllowedAction(SertAction $allowedAction)
    {
        $this->allowed_actions[] = $allowedAction;

        return $this;
    }

    /**
     * Remove allowedAction
     *
     * @param \AppBundle\Entity\SertAction $allowedAction
     */
    public function removeAllowedAction(SertAction $allowedAction)
    {
        $this->allowed_actions->removeElement($allowedAction);
    }

    /**
     * Get allowedActions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllowedActions()
    {
        return $this->allowed_actions;
    }

    /**
     * @param can
     */
    public function setOthersEditible($value)
    {
        $this->can_edit_others = $value;
    }

    /**
     * @return boolean
     */
    public function canEditOthers()
    {
        return $this->can_edit_others;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return UserGroup
     */
    public function setDisplayName($displayName)
    {
        $this->display_name = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * Set canEditOthers
     *
     * @param boolean $canEditOthers
     *
     * @return UserGroup
     */
    public function setCanEditOthers($canEditOthers)
    {
        $this->can_edit_others = $canEditOthers;

        return $this;
    }

    /**
     * Get canEditOthers
     *
     * @return boolean
     */
    public function getCanEditOthers()
    {
        return $this->can_edit_others;
    }
}
