<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\SertState;

/**
 * Class SertAction
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="SertAction")
 */
class SertAction
{
    /**
     * @ORM\Column(name="ID_ActionName", type="string", length=30)
     * @ORM\Id
     */
    private $action_name;

    /**
     * @ORM\Column(name="icon", type="string", length=100)
     */
    private $icon;

    /**
     * @ORM\Column(name="display_name", type="string", length=50)
     */
    private $display_name;
    
    /**
     * @ORM\ManyToMany(targetEntity="SertState", inversedBy="allowed_actions")
     * @ORM\JoinTable(name="states_actions",
     *                joinColumns={@ORM\JoinColumn(name="ID_ActionName", referencedColumnName="ID_ActionName")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="ID_SertState", referencedColumnName="ID_SertState")}
     *               )
     */
    private $allowed_states;

    /**
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="allowed_actions")
     * @ORM\JoinTable(name="groups_actions",
     *                joinColumns={@ORM\JoinColumn(name="ID_ActionName", referencedColumnName="ID_ActionName")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="ID_UserGroup", referencedColumnName="ID_UserGroup")}
     *               )
     */
    private $allowed_groups;

    public function __construct()
    {
        $this->states = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set actionName
     *
     * @param string $actionName
     *
     * @return SertAction
     */
    public function setActionName($actionName)
    {
        $this->action_name = $actionName;

        return $this;
    }

    /**
     * Get actionName
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action_name;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return SertAction
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return SertAction
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
     * Add state
     *
     * @param \AppBundle\Entity\SertState $state
     *
     * @return SertAction
     */
    public function addState(SertState $state)
    {
        $this->allowed_states[] = $state;

        return $this;
    }

    /**
     * Remove state
     *
     * @param \AppBundle\Entity\SertState $state
     */
    public function removeState(SertState $state)
    {
        $this->allowed_states->removeElement($state);
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStates()
    {
        return $this->allowed_states;
    }

    /**
     * Add allowedGroup
     *
     * @param \AppBundle\Entity\UserGroup $allowedGroup
     *
     * @return SertAction
     */
    public function addAllowedGroup(\AppBundle\Entity\UserGroup $allowedGroup)
    {
        $this->allowed_groups[] = $allowedGroup;

        return $this;
    }

    /**
     * Remove allowedGroup
     *
     * @param \AppBundle\Entity\UserGroup $allowedGroup
     */
    public function removeAllowedGroup(\AppBundle\Entity\UserGroup $allowedGroup)
    {
        $this->allowed_groups->removeElement($allowedGroup);
    }

    /**
     * Get allowedGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllowedGroups()
    {
        return $this->allowed_groups;
    }
}
