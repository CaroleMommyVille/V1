<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NothingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('message', 'textarea', array(
                'required' => true,
                'attr' => array(
                    'class' => 'wide thick',
                    ),
                )
            )
            ->add('keep', 'checkbox', array(
                'required' => false,
                )
            );
    }

    public function getName() {
        return 'rien';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'rien',
         ));
    }

}