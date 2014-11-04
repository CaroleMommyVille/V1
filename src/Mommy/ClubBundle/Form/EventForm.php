<?php

namespace Mommy\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityManager;

class EventForm extends AbstractType
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
            ->add('name', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => "Quel est le nom de l'évènement ?",
                    )
                )
            )
            ->add('date', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => "Quelle est la date de l'évènement ?",
                    'class' => 'with-datepicker',
                    'readonly' => 'readonly',
                    'data-calendar' => 'true',
                    ),
                )
            )
            ->add('description', 'textarea', array(
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Commencer à écrire',
                        'class' => 'post',
                    ),
                )
            );
    }

    public function getName() {
        return 'event';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class'      => 'Mommy\ClubBundle\Entity\Event',
            'intention'       => 'event',
        ));
    }

}