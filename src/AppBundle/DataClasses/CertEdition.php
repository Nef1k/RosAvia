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
     * @Assert\All({
     *     @Assert\NotBlank(message="Значение не может быть пустым."),
     *     @Assert\Type(
     *          type="integer",
     *          message="Значение не может быть не числом.")
     * })
     * @Assert\NotNull(message = "Значение не может быть нулевым")
     */
    private $cert_id;
    

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
}