<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IdentityForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lastname', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Quel est votre nom ?',
                    )
                )
            )
            ->add('firstname', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Quel est votre prénom ?',
                    )
                )
            )
            ->add('email', 'email', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Quel est votre email ?',
                    )
                )
            )
            ->add('password', 'password', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Choisissez un mot de passe',
                    )
                )
            )
            ->add('birthday', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Quelle est votre date de naissance ?',
                    'class' => 'birthday with-datepicker',
                    'readonly' => 'readonly',
                    'data-calendar' => 'true',
                    ),
                )
            )
            ->add('cnil', 'checkbox', array(
                'required' => false,
                )
            );
    }

    public function getName() {
        return 'identity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'data_class'      => 'Mommy\SecurityBundle\Entity\User',
            'intention'       => 'identity',
        ));
    }

}