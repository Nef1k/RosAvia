<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.10.2016
 * Time: 14:36
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_type")
 */
class FileType
{
    /**
     * @ORM\Column(name="ID_FileType", type="integer")
     * @ORM\Id
     */
    private $ID_FileType;

    /**
     * @ORM\Column(name="TypeValue", type="string")
     */
    private $TypeValue;

    /**
     * Set iDFileType
     *
     * @param integer $iDFileType
     *
     * @return FileType
     */
    public function setIDFileType($iDFileType)
    {
        $this->ID_FileType = $iDFileType;

        return $this;
    }

    /**
     * Get iDFileType
     *
     * @return integer
     */
    public function getIDFileType()
    {
        return $this->ID_FileType;
    }

    /**
     * Set typeValue
     *
     * @param string $typeValue
     *
     * @return FileType
     */
    public function setTypeValue($typeValue)
    {
        $this->TypeValue = $typeValue;

        return $this;
    }

    /**
     * Get typeValue
     *
     * @return string
     */
    public function getTypeValue()
    {
        return $this->TypeValue;
    }
}
