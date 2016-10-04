<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 17.09.2016
 * Time: 21:28
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Sertificate;

/**
 * @Annotation
 *
 */
class CheckCertsStateValidator extends ConstraintValidator
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
     * @param mixed $cert_ids The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($cert_ids, Constraint $constraint)
    {
        if($cert_ids != null){
            /** @var  $certs_list Sertificate[]*/
            $certs_list = $this->em->getRepository("AppBundle:Sertificate")->findBy(array('ID_Sertificate' => $cert_ids));
            /** @var  $cert Sertificate*/
            $wrong_status_ids = [];
            foreach ($certs_list as $cert){
                if($cert->getIDSertState() != 4){
                    array_push($wrong_status_ids,$cert->getIDSertificate());
                }
            }
            if (count($wrong_status_ids) != 0){
                $this->context
                    ->buildViolation($constraint->message)
                    ->setInvalidValue($wrong_status_ids)
                    ->addViolation();
            }
        }
    }
}