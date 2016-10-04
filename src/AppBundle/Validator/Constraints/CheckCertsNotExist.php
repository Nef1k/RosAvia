<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 16.09.2016
 * Time: 19:09
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckCertsNotExist extends Constraint
{

    public $message = "Сертификаты с такими номерами существуют: {{value}}";

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

}