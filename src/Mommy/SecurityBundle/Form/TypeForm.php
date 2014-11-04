<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\TypeToNameTransformer;

use Doctrine\ORM\EntityManager;

class TypeForm extends AbstractType
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
        $transformer = new TypeToNameTransformer($this->entityManager);
        $builder
            ->add(
                $builder
                    ->create('type', 'hidden', array(
                        'required' => true,
                    ))
                    ->addModelTransformer($transformer)
            );
    }

    public function getName() {
        return 'type';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'data_class'      => 'Mommy\ProfilBundle\Entity\UserType',
            'intention'       => 'type',
        ));
    }

}