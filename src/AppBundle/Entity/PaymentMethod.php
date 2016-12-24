<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 14:44
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_method")
 */

class PaymentMethod
{
    /**
     * @ORM\Column(name="ID_PaymentMethod", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ID_PaymentMethod;

    /**
     * @ORM\Column(name="PaymentMethodName", type="string")
     */
    private $PaymentMethodName;


    /**
     * Get iDPaymentMethod
     *
     * @return integer
     */
    public function getIDPaymentMethod()
    {
        return $this->ID_PaymentMethod;
    }

    /**
     * Set paymentMethodName
     *
     * @param string $paymentMethodName
     *
     * @return PaymentMethod
     */
    public function setPaymentMethodName($paymentMethodName)
    {
        $this->PaymentMethodName = $paymentMethodName;

        return $this;
    }

    /**
     * Get paymentMethodName
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        return $this->PaymentMethodName;
    }
}
