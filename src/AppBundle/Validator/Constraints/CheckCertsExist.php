<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 12:29
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckCertsExist extends Constraint
{

    public $message = 'Сертификаты с такими номерами не существуют: "%value%"';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

}