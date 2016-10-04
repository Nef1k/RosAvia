<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CertificateEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
            add("name", TextType::class, array(
                "label" => "Имя",
                "required" => "false",
            ))->
            add("last_name", TextType::class, array(
                "label" => "Фамилия",
                "required" => "false",
            ))->
            add("phone_number", TextType::class, array(
                "label" => "Телефон",
                "required" => "false",
            ))->
            add("flight_type", EntityType::class, array(
                "class" => "AppBundle:FlightType",
                "required" => "false",
                "placeholder" => "Не выбран",
                "label" => "Тип полёта",
                "choice_label" => "name",
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            "data_class" => "AppBundle\\Entity\\Sertificate",
        ));
    }
}