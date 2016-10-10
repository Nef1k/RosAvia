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
 * @ORM\Table(name="file")
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
}