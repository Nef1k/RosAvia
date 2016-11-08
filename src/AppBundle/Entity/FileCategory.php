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
}
