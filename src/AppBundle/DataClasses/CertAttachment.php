<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 12:15
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;


class CertAttachment
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
     * @MyAssert\CheckCertsExist()
     * @MyAssert\CheckCertState()
     * 
     */
    
    protected $cert_ids = array();
    /**
     * @Assert\Type(
     *     message = "Значение не может быть не числом.",
     *     type = "digit")
     * @Assert\NotBlank(message = "Значение не может быть пустым.")
     * @Assert\NotNull(message = "Значение не может быть нулевым")
     * 
     * @MyAssert\CheckUserID()
     */
    protected $user_id;

    /**
     * @return array
     */
    public function getCertIds()
    {
        return $this->cert_ids;
    }

    /**
     * @param array $cert_ids
     * @return $this
     */
    public function setCertIds($cert_ids)
    {
        $this->cert_ids = $cert_ids;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }
}