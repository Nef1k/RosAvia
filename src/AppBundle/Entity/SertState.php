<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\SertAction;

/**
    *@ORM\Entity
    *@ORM\Table(name="sertificate_state")
    */
class SertState
{
    /**
      * @ORM\Column(name="ID_SertState", type="integer")
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="AUTO")
      */
    private $ID_SertState;

    /**
      * @ORM\Column(type="string", length=30)
      */
    private $name;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $icon;

    /**
     * @ORM\ManyToMany(targetEntity="SertAction", mappedBy="allowed_states")
     */
    private $allowed_actions;

    public function __construct()
    {
        $this->allowed_actions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getIDSertState()
    {
        return $this->ID_SertState;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SertState
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
     * Add action
     *
     * @param \AppBundle\Entity\SertAction $action
     *
     * @return SertState
     */
    public function addAction(SertAction $action)
    {
        $this->allowed_actions[] = $action;

        return $this;
    }

    /**
     * Remove action
     *
     * @param \AppBundle\Entity\SertAction $action
     */
    public function removeAction(SertAction $action)
    {
        $this->allowed_actions->removeElement($action);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActions()
    {
        return $this->allowed_actions;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return SertState
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
     * Add allowedAction
     *
     * @param \AppBundle\Entity\SertAction $allowedAction
     *
     * @return SertState
     */
    public function addAllowedAction(\AppBundle\Entity\SertAction $allowedAction)
    {
        $this->allowed_actions[] = $allowedAction;

        return $this;
    }

    /**
     * Remove allowedAction
     *
     * @param \AppBundle\Entity\SertAction $allowedAction
     */
    public function removeAllowedAction(\AppBundle\Entity\SertAction $allowedAction)
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
}
