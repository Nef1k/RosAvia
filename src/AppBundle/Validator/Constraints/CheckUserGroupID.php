<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 18.09.2016
 * Time: 14:41
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class CheckUserGroupID extends Constraint
{
    public $message = "Такой группы пользователей не существует.";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}