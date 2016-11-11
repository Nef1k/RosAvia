<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 11.11.2016
 * Time: 16:21
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckFilesExist extends Constraint
{

    public $message = 'Файлы с такими номерами не существуют: "%value%"';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

}