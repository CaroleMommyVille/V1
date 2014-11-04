<?php

namespace Mommy\SondageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SondageBundle\Form\DataTransformer\JobActivitiesToNameTransformer;
use Mommy\SondageBundle\Form\DataTransformer\AddressToLiteralTransformer;

use Doctrine\ORM\EntityManager;

class SondageForm extends AbstractType
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
        $builder
            ->add('oui', 'checkbox', array(
                'required' => false,
            ))
            ->add('non', 'checkbox', array(
                'required' => false,
            ))
            ->add('inconnu', 'checkbox', array(
                'required' => false,
            ));
    }

    public function getName() {
        return 'sondage';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'intention'       => 'sondage',
        ));
    }

}