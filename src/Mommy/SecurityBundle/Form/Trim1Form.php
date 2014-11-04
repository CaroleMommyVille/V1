<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyStatusToNameTransformer;

use Doctrine\ORM\EntityManager;

class Trim1Form extends AbstractType
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
        $bool = new BooleanToNameTransformer($this->entityManager);
        $status = new PregnancyStatusToNameTransformer($this->entityManager);
        $builder
            ->add(
                $builder
                    ->create('consult1', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('status', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($status)
            )
            ->add('PathologyPregnancy', 'text', array(
                'required' => false,
            ))
            ->add('PathologyBaby', 'text', array(
                'required' => false,
            ))
            ->add('PregnancySymptoms', 'text', array(
                'required' => false,
            ));
    }

    public function getName() {
        return 'trim1';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'trim1',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Quarter1',
        ));
    }

}