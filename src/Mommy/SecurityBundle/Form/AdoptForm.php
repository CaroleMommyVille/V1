<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptApprovalToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptAnotherWayToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptApprovalEaseToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptApprovalDenialToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptAgeToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptTypeToNameTransformer;

use Doctrine\ORM\EntityManager;

class AdoptForm extends AbstractType
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
        $choices = array();
//        $mem = new \Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->container->get('session');
        
        $choices['agrement'] = $mem->get('profil-form-adoptante-agrement');
        if (!$choices['agrement']) {
            $agrement = $mem->get('profil-adopt-approval');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptApproval')->findAll();
                $mem->set('profil-adopt-approval', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['agrement'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-agrement', $choices['agrement'], 0);
        }
        $choices['autrement'] = $mem->get('profil-form-adoptante-autrement');
        if (!$choices['autrement']) {
            $agrement = $mem->get('profil-adopt-another-way');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptAnotherWay')->findAll();
                $mem->set('profil-adopt-another-way', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['autrement'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-autrement', $choices['autrement'], 0);
        }
        $choices['facilite'] = $mem->get('profil-form-adoptante-facilite');
        if (!$choices['facilite']) {
            $agrement = $mem->get('profil-adopt-approval-ease');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptApprovalEase')->findAll();
                $mem->set('profil-adopt-approval-ease', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['facilite'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-facilite', $choices['facilite'], 0);
        }
        $choices['refus'] = $mem->get('profil-form-adoptante-refus');
        if (!$choices['refus']) {
            $agrement = $mem->get('profil-adopt-approval-denial');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptApprovalDenial')->findAll();
                $mem->set('profil-adopt-approval-denial', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['refus'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-refus', $choices['refus'], 0);
        }
        $choices['type'] = $mem->get('profil-form-adoptante-type');
        if (!$choices['type']) {
            $agrement = $mem->get('profil-adopt-type');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptType')->findAll();
                $mem->set('profil-adopt-type', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['type'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-type', $choices['type'], 0);
        }
        $choices['age'] = $mem->get('profil-form-adoptante-age');
        if (!$choices['age']) {
            $agrement = $mem->get('profil-adopt-age');
            if (is_null($agrement) || !$agrement) {
                $agrement = $this->entityManager->getRepository('MommyProfilBundle:AdoptAge')->findAll();
                $mem->set('profil-adopt-age', $agrement);
            }
            foreach ($agrement as $item) {
                $choices['age'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-adoptante-age', $choices['age'], 0);
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
                    '78' => '6 ans et demi', 
                    '84' => '7 ans', 
                    '90' => '7 ans et demi', 
                    '96' => '8 ans et plus'
                );

        $country = $this->entityManager->getRepository('MommyProfilBundle:AdoptCountry')->findAll();
        foreach ($country as $item) {
            if ($item->getDescFR() == 'France') continue;
            $choices['country'][$item->getCategory()][$item->getName()] = $item->getDescFR();
        }

        $mem = null; unset($mem);

        $bool = new BooleanToNameTransformer($this->entityManager);
        $approval = new AdoptApprovalToNameTransformer($this->entityManager);
        $anotherway = new AdoptAnotherWayToNameTransformer($this->entityManager);
        $ease = new AdoptApprovalEaseToNameTransformer($this->entityManager);
        $denial = new AdoptApprovalDenialToNameTransformer($this->entityManager);
        $type = new AdoptTypeToNameTransformer($this->entityManager);
        $age = new AdoptAgeToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('arrived', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('started', 'choice', array(
                'required' => false,
                'choices' => $choices['month'], 
                'attr' => array(
                    'placeholder' => 'Choisissez une période',
                    ),
            ))
            ->add('lasted', 'choice', array(
                'required' => false,
                'choices' => $choices['month'],
                'attr' => array(
                    'placeholder' => 'Choisissez une durée',
                    ),
            ))
            ->add(
                $builder
                    ->create('approval', 'choice', array(
                        'required' => false,
                        'choices' => $choices['agrement']
                    ))
                    ->addModelTransformer($approval)
            )
            ->add(
                $builder
                    ->create('resort', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('anotherway', 'choice', array(
                        'required' => false,
                        'choices' => $choices['autrement'],
                    ))
                    ->addModelTransformer($anotherway)
            )
            ->add(
                $builder
                    ->create('ease', 'choice', array(
                        'required' => false,
                        'choices' => $choices['facilite'],
                    ))
                    ->addModelTransformer($ease)
            )
            ->add(
                $builder
                    ->create('denial', 'choice', array(
                        'required' => false,
                        'choices' => $choices['refus'],
                    ))
                    ->addModelTransformer($denial)
            )
            ->add(
                $builder
                    ->create('type', 'choice', array(
                        'required' => false,
                        'choices' => $choices['type'],
                    ))
                    ->addModelTransformer($type)
            )
            ->add(
                $builder
                    ->create('age', 'choice', array(
                        'required' => false,
                        'choices' => $choices['age'],
                    ))
                    ->addModelTransformer($age)
            )
            ->add(
                $builder
                    ->create('children', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('countries', 'choice', array(
                'required' => false,
                'multiple' => true,
                'choices' => $choices['country'],
            ))
            ->add(
                $builder
                    ->create('french', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            );
    }

    public function getName() {
        return 'adoptante';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Adoption',
            'intention'       => 'adoptante',
        ));
    }

}