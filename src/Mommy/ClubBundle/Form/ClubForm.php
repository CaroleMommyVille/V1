<?php

namespace Mommy\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\ClubBundle\Form\DataTransformer\AddressToLiteralTransformer;

use Doctrine\ORM\EntityManager;

class ClubForm extends AbstractType
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
        $address = new AddressToLiteralTransformer($this->entityManager);
        $builder
            ->add('name', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'h1',
                    'placeholder' => 'Nom du Club',
                    )
                )
            )
            ->add('description', 'textarea', array(
                'required' => true,
                'attr' => array(
                    'class' => 'post h2 ninety',
                    'placeholder' => 'Décrivez votre MommyClub en quelques lignes',
                    )
                )
            )
            ->add(
                $builder
                    ->create('address', 'text', array(
                        'required' => false,
                        'attr' => array(
                            'class' => 'address h2',
                            'placeholder' => 'Adresse du Club si elle existe',
                            ),
                    ))
                    ->addModelTransformer($address)
            )
            ->add('photo', 'file', array(
                'required' => false,
                'attr' => array(
                    'class' => 'ninety',
                    )
                )
            )
            ->add('keywords', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'ninety',
                    'placeholder' => 'Listez vos mots-clés',
                    )
                )
            );
    }

    public function getName() {
        return 'club';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'intention'       => 'club',
        ));
    }

}