<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 21:28
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckCertsState extends Constraint
{

    public $message = 'Некоторые сертификаты имеют неверный статус для оплаты.';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}