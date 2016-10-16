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
class File implements \Serializable
{
    /**
     * @ORM\Column(name="ID_File", type="integer")
     * @ORM\Id
     */
    private $ID_File;


    /**
     * @ORM\Column(type="datetime", nullable=true)
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
    private $User;

    /**
     * @ORM\Column(name="DisplayName", nullable=true)
     */
    private $DisplayName;

    /**
     * @ORM\ManyToOne(targetEntity="FileCategory")
     * @ORM\JoinColumn(name="ID_Category", referencedColumnName="ID_Category", nullable=false)
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity="FileType")
     * @ORM\JoinColumn(name="ID_FileType", referencedColumnName="ID_FileType", nullable=false)
     */
    private $FileType;

    /**
     * @ORM\Column(name="FileSize", nullable=false, type="integer")
     */
    private $FileSize;
    
    
    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    /**
     * @param mixed $FileSize
     * @return $this
     */
    public function setFileSize($FileSize)
    {
        $this->FileSize = $FileSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->FileSize;
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }

    /**
     * Set iDFile
     *
     * @param integer $iDFile
     *
     * @return File
     */
    public function setIDFile($iDFile)
    {
        $this->ID_File = $iDFile;

        return $this;
    }

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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return File
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->User = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\FileCategory $category
     *
     * @return File
     */
    public function setCategory(FileCategory $category)
    {
        $this->Category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\FileCategory
     */
    public function getCategory()
    {
        return $this->Category;
    }

    /**
     * Set fileType
     *
     * @param \AppBundle\Entity\FileType $fileType
     *
     * @return File
     */
    public function setFileType(FileType $fileType)
    {
        $this->FileType = $fileType;

        return $this;
    }

    /**
     * Get fileType
     *
     * @return \AppBundle\Entity\FileType
     */
    public function getFileType()
    {
        return $this->FileType;
    }
}
