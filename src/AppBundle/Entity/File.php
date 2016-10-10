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
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="ID_Category", referencedColumnName="ID_Category", nullable=false)
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity="FileType")
     * @ORM\JoinColumn(name="ID_FileType", referencedColumnName="ID_FileType", nullable=false)
     */
    private $FileType;
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
}