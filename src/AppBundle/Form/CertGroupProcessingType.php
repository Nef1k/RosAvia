<?php

namespace AppBundle\Form;

use AppBundle\Entity\SertAction;
use AppBundle\Entity\Sertificate;
use AppBundle\Stuff\CertificateStuff;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;

use Doctrine\ORM\EntityManager;

class CertGroupProcessingType extends AbstractType
{
    /** @var Sertificate[] */
    private $certificates;

    /** @var CertificateStuff */
    private $certificateStuff;

    public function __construct(CertificateStuff $certificateStuff)
    {
        $this->certificateStuff = $certificateStuff;
        //$this->certificates = $certificates;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute("action", "sdf.php");

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            /** @var $certificates Sertificate[] */
            $data = $event->getData();
            $certificates = $data["certificates"];
            $actions = $this->certificateStuff->getAvailableActions($data["state_id"]);

            $event->getForm()->
            add("action", ChoiceType::class, array(
                "label" => "Действие",
                "choices" => $actions,
                "choice_label" => function($action){
                    /** @var $action SertAction */
                    return ($action != null) ? $action->getDisplayName() : "";
                },
                "choice_value" => function($action){
                    /** @var $action SertAction */
                    return ($action != null) ? $action->getActionName() : "";
                }
            ))->
            add("certificates", ChoiceType::class, array(
                "label" => "Сертификаты",
                "expanded" => "true",
                "multiple" => "tue",
                "choices" => $certificates,
                "choice_label" => function($certificate){
                    /** @var $certificate Sertificate */
                    return ($certificate != null) ? $certificate->getName() : "";
                },
                "choice_value" => function($certificate){
                    /** @var $certificate Sertificate */
                    return ($certificate != null) ? $certificate->getIDSertificate() : "";
                }
            ));
        });
    }
}