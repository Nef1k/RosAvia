<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 16.09.2016
 * Time: 19:27
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Sertificate;

class CheckCertsNotExistValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $cert_ID_array
     * @param Constraint $constraint The constraint for the validation
     * @internal param mixed $value The value that should be validated
     */
    public function validate($cert_ID_array, Constraint $constraint)
    {
        if($cert_ID_array != null){
            /** @var  $certs_list Sertificate[]*/
            $certs_list = $this->em->getRepository("AppBundle:Sertificate")->findBy(array('ID_Sertificate' => $cert_ID_array));
            /** @var  $cert Sertificate*/
            if (count($certs_list)) {
                $exist_certs = [];
                foreach ($certs_list as $cert) {
                    if (in_array($cert->getIDSertificate(), $cert_ID_array)) {
                        array_push($exist_certs,$cert->getIDSertificate());
                    }
                }
                $this->context->buildViolation($constraint->message)->setInvalidValue($exist_certs)
                    ->addViolation();
            }
        }
    }
}