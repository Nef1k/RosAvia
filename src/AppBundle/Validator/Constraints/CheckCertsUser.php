<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 18.09.2016
 * Time: 1:12
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckCertsUser extends Constraint
{

    public $message = 'Некоторые сертификаты привязаны к другим пользователям.';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}