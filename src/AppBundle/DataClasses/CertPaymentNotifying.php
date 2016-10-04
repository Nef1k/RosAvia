<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 20:44
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;


class CertPaymentNotifying
{
    /**
     * @var array
     * @Assert\All({
     *     @Assert\NotBlank(message="Значение не может быть пустым."),
     *     @Assert\Type(
     *          type="integer",
     *          message="Значение не может быть не числом.")
     * })
     * @Assert\NotNull(message = "Значение не может быть нулевым")
     * @MyAssert\CheckCertsExist()
     * @MyAssert\CheckCertsState()
     * @MyAssert\CheckCertsUser()
     * 
     */
    protected $cert_ids = array();

    /**
     * @param array $cert_ids
     */
    public function setCertIds($cert_ids)
    {
        $this->cert_ids = $cert_ids;
    }

    /**
     * @return array
     */
    public function getCertIds()
    {
        return $this->cert_ids;
    }
}