<?php

namespace Mommy\ProfilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\ProfilBundle\Form\DataTransformer\AddressToLiteralTransformer;

use Doctrine\ORM\EntityManager;

class ProfilForm extends AbstractType
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
        $request = $this->container->get('request');
        $session = $request->getSession();

        $address = new AddressToLiteralTransformer($this->entityManager);

        $user = $this->entityManager->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
        $home = null;
        if (is_object($user->getAddress()))
            $home = $user->getAddress();
        $builder
            ->add('firstname', 'text', array(
                'required' => true,
                'data' => $user->getFirstname(),
                )
            )
            ->add('lastname', 'text', array(
                'required' => true,
                'data' => $user->getLastname(),
                )
            )
            ->add('email', 'text', array(
                'required' => true,
                'data' => $user->getEmail(),
                )
            )
            ->add('description', 'textarea', array(
                'required' => true,
                'data' => html_entity_decode($user->getDescription(), ENT_QUOTES),
                'attr' => array(
                    'placeholder' => 'Décrivez-vous en quelques lignes',
                    )
                )
            )
            ->add(
                $builder
                    ->create('address', 'text', array(
                        'required' => true,
                        'attr' => array(
                            'placeholder' => '',
                            'class' => 'address',
                            ),
                        'data' => $home,
                        'data_class' => null,
                    ))
                    ->addModelTransformer($address)
            )
            ->add('photo', 'file', array(
                'required' => false,
                )
            );
    }

    public function getName() {
        return 'profil';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'intention'       => 'profil',
        ));
    }

}