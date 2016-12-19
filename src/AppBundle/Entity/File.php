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

    /**
     * Get iDFile
     *
     * @return integer
     */
    public function getIDFile()
    {
        return $this->ID_File;
    }

    /**
     * Set fileDate
     *
     * @param \DateTime $fileDate
     *
     * @return File
     */
    public function setFileDate($fileDate)
    {
        $this->FileDate = $fileDate;

        return $this;
    }

    /**
     * Get fileDate
     *
     * @return \DateTime
     */
    public function getFileDate()
    {
        return $this->FileDate;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->FileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->FileName;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return File
     */
    public function setDisplayName($displayName)
    {
        $this->DisplayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->DisplayName;
    }

    /**
     * Set realCategory
     *
     * @param string $realCategory
     *
     * @return File
     */
    public function setRealCategory($realCategory)
    {
        $this->RealCategory = $realCategory;

        return $this;
    }

    /**
     * Get realCategory
     *
     * @return string
     */
    public function getRealCategory()
    {
        return $this->RealCategory;
    }

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     *
     * @return File
     */
    public function setFileSize($fileSize)
    {
        $this->FileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->FileSize;
    }

    /**
     * Set iDUser
     *
     * @param \AppBundle\Entity\User $iDUser
     *
     * @return File
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
     * Set iDCategory
     *
     * @param \AppBundle\Entity\FileCategory $iDCategory
     *
     * @return File
     */
    public function setIDCategory(\AppBundle\Entity\FileCategory $iDCategory)
    {
        $this->ID_Category = $iDCategory;

        return $this;
    }

    /**
     * Get iDCategory
     *
     * @return \AppBundle\Entity\FileCategory
     */
    public function getIDCategory()
    {
        return $this->ID_Category;
    }

    /**
     * Set iDFileType
     *
     * @param \AppBundle\Entity\FileType $iDFileType
     *
     * @return File
     */
    public function setIDFileType(\AppBundle\Entity\FileType $iDFileType)
    {
        $this->ID_FileType = $iDFileType;

        return $this;
    }

    /**
     * Get iDFileType
     *
     * @return \AppBundle\Entity\FileType
     */
    public function getIDFileType()
    {
        return $this->ID_FileType;
    }
}
