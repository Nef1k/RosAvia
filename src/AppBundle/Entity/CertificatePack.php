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
}