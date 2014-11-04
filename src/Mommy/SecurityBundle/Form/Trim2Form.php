<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyResultToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyStatusToNameTransformer;

use Doctrine\ORM\EntityManager;

class Trim2Form extends AbstractType
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
        $result = new PregnancyResultToNameTransformer($this->entityManager);
        $bool = new BooleanToNameTransformer($this->entityManager);
        $status = new PregnancyStatusToNameTransformer($this->entityManager);
        $builder
            ->add(
                $builder
                    ->create('result', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($result)
            )
            ->add(
                $builder
                    ->create('stopped', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('JobInformed', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('consult2', 'hidden', array(
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
        return 'trim2';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'trim2',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Quarter2',
        ));
    }

}