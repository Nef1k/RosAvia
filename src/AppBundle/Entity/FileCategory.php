<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.10.2016
 * Time: 14:23
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_category")
 */
class FileCategory
{
    /**
     * @ORM\Column(name="ID_Category", type="integer")
     * @ORM\Id
     */
    private $ID_Category;

    /**
     * @ORM\Column(name="CategoryName", type="string")
     */
    private $CategoryName;

    /**
     * Set iDCategory
     *
     * @param integer $iDCategory
     *
     * @return FileCategory
     */
    public function setIDCategory($iDCategory)
    {
        $this->ID_Category = $iDCategory;

        return $this;
    }

    /**
     * Get iDCategory
     *
     * @return integer
     */
    public function getIDCategory()
    {
        return $this->ID_Category;
    }

    /**
     * Set categoryName
     *
     * @param string $categoryName
     *
     * @return FileCategory
     */
    public function setCategoryName($categoryName)
    {
        $this->CategoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->CategoryName;
    }
}
