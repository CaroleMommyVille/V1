<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\TryPMAToNameTransformer;

use Doctrine\ORM\EntityManager;

class PMAForm extends AbstractType
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
        $choices = array();
//        $mem = new \Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->container->get('session');
        
        $choices['tries'] = $mem->get('profil-form-pma-tries');
        if (!$choices['tries']) {
            $tries = $mem->get('profil-try-pma');
            if (is_null($tries) || !$tries) {
                $tries = $this->entityManager->getRepository('MommyProfilBundle:TryPMA')->findAll();
                $mem->set('profil-try-pma', $tries);
            }
            foreach ($tries as $item) {
                $choices['tries'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-tries', $choices['tries'], 0);
        }

        $choices['month'] = array(
                    '1' => '1 mois', 
                    '2' => '2 mois',
                    '3' => '3 mois', 
                    '4' => '4 mois', 
                    '5' => '5 mois', 
                    '6' => '6 mois', 
                    '7' => '7 mois', 
                    '8' => '8 mois', 
                    '9' => '9 mois', 
                    '10' => '10 mois', 
                    '11' => '11 mois', 
                    '12' => '1 an', 
                    '18' => '1 an et demi', 
                    '24' => '2 ans', 
                    '30' => '2 ans et demi', 
                    '36' => '3 ans', 
                    '42' => '3 ans et demi', 
                    '48' => '4 ans', 
                    '54' => '4 ans et demi', 
                    '60' => '5 ans', 
                    '66' => '5 ans et demi', 
                    '72' => '6 ans', 
                    '78' => '6 ans et plus', 
                );

        $mem = null; unset($mem);

        $bool = new BooleanToNameTransformer($this->entityManager);
        $tries = new TryPMAToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('pregnant', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('date', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-delivery with-datepicker',
                    'data-calendar' => 'true',
                    'readonly' => 'readonly',
                    ),
            ))
            ->add('amenorrhee', 'choice', array(
                'required' => false,
                'choices' => range(0,43),
                'empty_value' => "Choisissez un nombre de semaines",
            ))
            ->add('PathologyGyneco', 'text', array(
                'required' => false,
            ))
            ->add('RiskFactor', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('stimulation', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('StimulationLength', 'choice', array(
                'required' => false,
                'choices' => $choices['month'],
                'empty_value' => "Choisissez une période",
            ))
            ->add('OvulationStimulator', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('pump', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('effet', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('PumpSideEffect', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('soft', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('SoftMethod', 'text', array(
                'required' => false,
            ))
            ->add('TechPMA', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('tries', 'choice', array(
                        'required' => false,
                        'choices' => $choices['tries'],
                    ))
                    ->addModelTransformer($tries)
            );

            if (($almost = $this->entityManager->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($this->entityManager->getRepository('MommySecurityBundle:User')->find($session->get('uid')))) !== null) {
                $builder
                    ->add('since', 'choice', array(
                        'required' => false,
                        'choices' => $choices['month'],
                    ));
            }
    }

    public function getName() {
        return 'pma';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'data_class'      => 'Mommy\ProfilBundle\Entity\PMA',
            'intention'       => 'pma',
        ));
    }

}