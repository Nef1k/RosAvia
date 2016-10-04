<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.09.2016
 * Time: 14:11
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckCertState extends Constraint
{

    public $message = 'Сертификаты с такими номерами уже привязаны к другим пользователям.';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

}