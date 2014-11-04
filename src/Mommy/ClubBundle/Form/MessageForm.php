<?php

namespace Mommy\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityManager;

class MessageForm extends AbstractType
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
            ->add('content', 'textarea', array(
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Commencer à écrire',
                        'class' => 'post',
                    ),
                )
            )
            ->add('image', 'file', array(
                'required' => false,
                'attr' => array(
                    'class' => 'hidden',
                    ),
                )
            )
            ->add('preview', 'hidden', array(
                'required' => false,
                )
            );
    }

    public function getName() {
        return 'message';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
//            'data_class'      => 'Mommy\ClubBundle\Entity\Message',
            'intention'       => 'message',
        ));
    }

}