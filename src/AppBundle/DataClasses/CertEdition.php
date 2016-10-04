<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.09.2016
 * Time: 13:21
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;

class CertEdition
{
    /**
     * @MyAssert\CheckCertsExist()
     * @Assert\NotBlank(message = "Значение не может быть пустым."),
     * @Assert\Type(
     *      message = "Значение не может быть не числом.",
     *      type = "integer")
     */
    private $cert_id;

    /**
     * @Assert\NotBlank(message = "Значение не может быть пустым."),
     * @Assert\Type(
     *     message = "Значение не может быть не числом.",
     *     type = "integer")
     */
    private $time;

    /**
     * @return mixed
     */
    public function getCertId()
    {
        return $this->cert_id;
    }

    /**
     * @param mixed $cert_id
     * @return $this
     */
    public function setCertId($cert_id)
    {
        $this->cert_id = $cert_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }
}