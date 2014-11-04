<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\SpeedToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ReactionToNameTransformer;

use Doctrine\ORM\EntityManager;

class PregnantForm extends AbstractType
{
    /**
     * The entity manager
     * @var EntityManager
     */
    private $entityManager;
    private $container;

    /**
     * @param EntityManager
     */
    public function __construct(EntityManager $entityManager, $container) {
        $this->entityManager =& $entityManager;
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->container =& $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $speed = new SpeedToNameTransformer($this->entityManager);
        $reaction = new ReactionToNameTransformer($this->entityManager);
        $builder
            ->add('date', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-delivery with-datepicker',
                    'readonly' => 'readonly',
                    ),
                )
            )
            ->add('amenorrhee', 'choice', array(
                'required' => false,
                'choices' => range(0,43),
                'empty_value' => "Choisissez un nombre de semaines",
                )
            )
            ->add(
                $builder
                    ->create('speed', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($speed)
            )
            ->add(
                $builder
                    ->create('reaction', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($reaction)
            )
            ->add('prems', 'hidden', array(
                'required' => false,
                )
            );
    }

    public function getName() {
        return 'enceinte';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Pregnancy',
            'intention'       => 'enceinte',
        ));
    }

}