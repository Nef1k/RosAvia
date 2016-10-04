<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 18.09.2016
 * Time: 1:17
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Sertificate;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Annotation
 *
 */
class CheckCertsUserValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $token;

    public function __construct(EntityManager $entityManager, TokenStorage $token)
    {
        $this->em = $entityManager;
        $this->token = $token;

    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $cert_ids The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($cert_ids, Constraint $constraint)
    {
        if($cert_ids != null){
            $user = $this->token->getToken()->getUser();
            /** @var  $certs_list Sertificate[]*/
            $certs_list = $this->em->getRepository("AppBundle:Sertificate")->findBy(array('ID_Sertificate' => $cert_ids));
            /** @var  $cert Sertificate*/
            $wrong_user_ids = [];
            foreach ($certs_list as $cert){
                if($cert->getUser() != $user){
                    array_push($wrong_user_ids,$cert->getIDSertificate());
                }
            }
            if (count($wrong_user_ids) != 0){
                $this->context
                    ->buildViolation($constraint->message)
                    ->setInvalidValue($wrong_user_ids)
                    ->addViolation();
            }
        }
    }
}