<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 11.09.2016
 * Time: 12:28
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckUserID extends Constraint
{
    public $NoUserMsg = "Пользователя с таким ID не существует.";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}