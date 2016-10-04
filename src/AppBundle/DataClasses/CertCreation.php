<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 16.09.2016
 * Time: 18:59
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;

class CertCreation
{

    /**
     * @var array
     * 
     * @Assert\All({
     *     @Assert\NotBlank(message="Значение не может быть пустым."),
     *     @Assert\Type(
     *          type="integer",
     *          message="Значение не может быть не числом.")
     * })
     * @Assert\NotNull(message = "Значение не может быть нулевым")
     * 
     * @MyAssert\CheckCertsNotExist()
     * 
     */
    protected $cert_id_array = array();

    /**
     * @return array
     */
    public function getCertIdArray()
    {
        return $this->cert_id_array;
    }

    /**
     * @param array $cert_id_array
     */
    public function setCertIdArray($cert_id_array)
    {
        $this->cert_id_array = $cert_id_array;
    }


}