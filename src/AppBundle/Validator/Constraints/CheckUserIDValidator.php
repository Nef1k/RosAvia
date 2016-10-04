<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 11.09.2016
 * Time: 11:48
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\AppBundle;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;


class CheckUserIDValidator extends ConstraintValidator
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function validate($value, Constraint $constraint){
        if($value!=null) {
            $user_list = $this->em->getRepository("AppBundle:User")->find($value);

            if (!($user_list)) {
                $this->context->buildViolation($constraint->NoUserMsg)
                    ->addViolation();
            }
        }
    }
        
    
}