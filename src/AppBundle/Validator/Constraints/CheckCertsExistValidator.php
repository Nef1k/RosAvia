<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 12:29
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Sertificate;

/**
 * Class CheckCertsExistValidator
 * @package AppBundle\Validator\Constraints
 */
class CheckCertsExistValidator extends ConstraintValidator
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
            if (count($certs_list) != count($cert_ID_array)) {
                $not_exist_certs = [];
                $exist_certs = array_column($certs_list,'ID_Sertificate');

                foreach ($exist_certs as $cert) {
                    if (!(in_array($cert, $cert_ID_array))) {
                        array_push($not_exist_certs,$cert);
                    }
                }
                $this->context
                    ->buildViolation($constraint->message)
                    ->setInvalidValue($not_exist_certs)
                    ->addViolation();
            }
        }
    }
}