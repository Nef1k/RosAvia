<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 13:34
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CertificatePack")
 */
class CertificatePack
{
    /**
     * @ORM\Column(name="ID_CertificatePack", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_CertificatePack;

    /**
     * @ORM\Column(name="CreationDate", type="datetime", nullable=true)
     */
    private $CreationDate;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_User", referencedColumnName="ID_User", nullable=false)
     */
    private $ID_User;

    /**
     * @ORM\Column(name="Count", nullable=false, type="integer")
     */
    private $Count;

    /**
     * Get iDCertificatePack
     *
     * @return integer
     */
    public function getIDCertificatePack()
    {
        return $this->ID_CertificatePack;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return CertificatePack
     */
    public function setCreationDate($creationDate)
    {
        $this->CreationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->CreationDate;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return CertificatePack
     */
    public function setCount($count)
    {
        $this->Count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->Count;
    }

    /**
     * Set iDUser
     *
     * @param \AppBundle\Entity\User $iDUser
     *
     * @return CertificatePack
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
}
