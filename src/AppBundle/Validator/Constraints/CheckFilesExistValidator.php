<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 11.11.2016
 * Time: 16:22
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\File;

class CheckFilesExistValidator extends ConstraintValidator
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
     * @param $file_ID_array
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($file_ID_array, Constraint $constraint)
    {
        if($file_ID_array != null){
            /** @var  $certs_list File[]*/
            $files_list = $this->em->getRepository("AppBundle:File")->findBy(array('ID_File' => $file_ID_array));
            /** @var  $cert File*/
            if (count($files_list) != count($file_ID_array)) {
                $not_exist_files = [];
                $exist_files = array_column($files_list,'ID_File');

                foreach ($exist_files as $file) {
                    if (!(in_array($file, $file_ID_array))) {
                        array_push($not_exist_certs,$cert);
                    }
                }
                $this->context
                    ->buildViolation($constraint->message)
                    ->setInvalidValue($not_exist_files)
                    ->addViolation();
            }
        }
    }
    

}