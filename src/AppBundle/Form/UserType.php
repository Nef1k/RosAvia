<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, array(
                "label" => "E-mail",
            ))
            ->add("username", TextType::class, array(
                "label" => "Имя пользователя",
            ))
            ->add("plainPassword", RepeatedType::class, array(
                "type" => PasswordType::class,
                "first_options" => array(
                    "label" => "Пароль"
                ),
                "second_options" => array(
                    "label" => "Повторите пароль"
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           "data_class" => 'AppBundle\Entity\User',
        ));
    }
}