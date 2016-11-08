<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.10.2016
 * Time: 14:13
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file")
 */
class File
{
    /**
     * @ORM\Column(name="ID_File", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_File;


    /**
     * @ORM\Column(name="FileDate", type="datetime", nullable=true)
     */
    private $FileDate;

    /**
     * @ORM\Column(name="FileName", type="string")
     */
    private $FileName;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ID_User", referencedColumnName="ID_User", nullable=false)
     */
    private $ID_User;

    /**
     * @ORM\Column(name="DisplayName", nullable=true)
     */
    private $DisplayName;

    /**
     * @ORM\ManyToOne(targetEntity="FileCategory")
     * @ORM\JoinColumn(name="ID_Category", referencedColumnName="ID_Category", nullable=false)
     */
    private $ID_Category;

    /**
     * @ORM\Column(name="RealCategory", type="string", nullable=true)
     */
    private $RealCategory;

    /**
     * @ORM\ManyToOne(targetEntity="FileType")
     * @ORM\JoinColumn(name="ID_FileType", referencedColumnName="ID_FileType", nullable=false)
     */
    private $ID_FileType;

    /**
     * @ORM\Column(name="FileSize", nullable=false, type="integer")
     */
    private $FileSize;
}
