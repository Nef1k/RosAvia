<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 11.11.2016
 * Time: 16:23
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;

class FileDeletion
{
    /**
     * @var array
     *
     * @Assert\All({
     *      @Assert\NotBlank(message = "Значение не может быть пустым."),
     *      @Assert\Type(
     *          message = "Значение не может быть не числом.",
     *          type = "integer")
     * })
     * @Assert\NotNull(message = "Значение не может быть нулевым")
     * @MyAssert\CheckFilesExist()
     *
     */
    protected $file_ids = array();

    /**
     * @return array
     */
    public function getFileIds()
    {
        return $this->file_ids;
    }

    /**
     * @param array $file_ids
     */
    public function setFileIds($file_ids)
    {
        $this->file_ids = $file_ids;
    }

}