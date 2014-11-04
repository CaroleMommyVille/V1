<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildSexToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AdoptCountryToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\SpeedToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ReactionToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MaternityToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyPreparationToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\DeliveryMethodToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildBreastfedToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildDiseaseToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildDiseaseHeartToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildDaycareToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\DeliveryMethodChangeToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildSchoolTypeToNameTransformer;

use Doctrine\ORM\EntityManager;

class ChildForm extends AbstractType
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

        $speed = $this->entityManager->getRepository('MommyProfilBundle:PregnancySpeed')->findAll();
            foreach ($speed as $item) {
                $choices['speed'][$item->getName()] = $item->getDescFR();
            }
        $speed = null;
        gc_collect_cycles();
        $reaction = $this->entityManager->getRepository('MommyProfilBundle:Reaction')->findAll();
        foreach ($reaction as $item) {
            if (($item->getName() == 'reaction-inconnu') || ($item->getName() == 'reaction-perdue')) continue;
            $choices['reaction'][$item->getName()] = $item->getDescFR();
        }
        $reaction = null;
        gc_collect_cycles();
        $country = $this->entityManager->getRepository('MommyProfilBundle:AdoptCountry')->findAll();
        foreach ($country as $item) {
            $choices['country'][$item->getName()] = $item->getDescFR();
        }
        $country = null;
        gc_collect_cycles();
        $breastfed = $this->entityManager->getRepository('MommyProfilBundle:ChildBreastfed')->findAll();
        foreach ($breastfed as $item) {
            if (($item->getName() == 'breastfed-choice') || ($item->getName() == 'breastfed-dunno')) continue;
            $choices['breastfed'][$item->getName()] = $item->getDescFR();
        }
        $breastfed = null;
        gc_collect_cycles();
        $heart = $this->entityManager->getRepository('MommyProfilBundle:ChildDiseaseHeart')->findAll();
        foreach ($heart as $item) {
            $choices['heart'][$item->getName()] = $item->getDescFR();
        }
        $heart = null;
        gc_collect_cycles();
        $change = $this->entityManager->getRepository('MommyProfilBundle:DeliveryMethodChange')->findAll();
        foreach ($change as $item) {
            $choices['change'][$item->getName()] = $item->getDescFR();
        }
        $change = null;
        gc_collect_cycles();
        $status = $this->entityManager->getRepository('MommyProfilBundle:PregnancyStatus')->findAll();
            foreach ($status as $item) {
        $choices['status'][$item->getName()] = $item->getDescFR();
        }
        $status = null;
        gc_collect_cycles();
        $daycare = $this->entityManager->getRepository('MommyProfilBundle:ChildDaycare')->findAll();
        foreach ($daycare as $item) {
            $choices['daycare'][$item->getName()] = $item->getDescFR();
        }
        $daycare = null;
        gc_collect_cycles();
        $school = $this->entityManager->getRepository('MommyProfilBundle:ChildSchoolType')->findAll();
        foreach ($school as $item) {
            $choices['school'][$item->getName()] = $item->getDescFR();
        }
        $school = null;
        gc_collect_cycles();

        $bool = new BooleanToNameTransformer($this->entityManager);
        $sex = new ChildSexToNameTransformer($this->entityManager);
        $country = new AdoptCountryToNameTransformer($this->entityManager);
        $speed = new SpeedToNameTransformer($this->entityManager);
        $reaction = new ReactionToNameTransformer($this->entityManager);
        $maternity = new MaternityToNameTransformer($this->entityManager);
        $preparation = new PregnancyPreparationToNameTransformer($this->entityManager);
        $method = new DeliveryMethodToNameTransformer($this->entityManager);
        $breastfed = new ChildBreastfedToNameTransformer($this->entityManager);
        $disease = new ChildDiseaseToNameTransformer($this->entityManager);
        $heart = new ChildDiseaseHeartToNameTransformer($this->entityManager);
        $daycare = new ChildDaycareToNameTransformer($this->entityManager);
        $change = new DeliveryMethodChangeToNameTransformer($this->entityManager);
        $school = new ChildSchoolTypeToNameTransformer($this->entityManager);

        $builder
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('sex', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($sex)
            )
            ->add('twin', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('adopted', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('country', 'choice', array(
                        'required' => false,
                        'choices' => $choices['country']
                    ))
                    ->addModelTransformer($country)
            )
            ->add('birthday', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'birthday-child with-datepicker',
                    'readonly' => 'readonly',
                    'data-calendar' => 'true',
                )
            ))
            ->add(
                $builder
                    ->create('pregnancyspeed', 'choice', array(
                        'required' => false,
                        'choices' => $choices['speed']
                    ))
                    ->addModelTransformer($speed)
            )
            ->add(
                $builder
                    ->create('reaction', 'choice', array(
                        'required' => false,
                        'choices' => $choices['reaction']
                    ))
                    ->addModelTransformer($reaction)
            )
            ->add(
                $builder
                    ->create('ondate', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('sa', 'choice', array(
                'required' => false,
                'choices' => range(26, 42),
            ))
            ->add(
                $builder
                    ->create('maternity', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($maternity)
            )
            ->add(
                $builder
                    ->create('preparation', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($preparation)
            )
            ->add(
                $builder
                    ->create('deliverymethod', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($method)
            )
            ->add(
                $builder
                    ->create('breastfed', 'choice', array(
                        'required' => false,
                        'choices' => $choices['breastfed'],
                    ))
                    ->addModelTransformer($breastfed)
            )
            ->add(
                $builder
                    ->create('sick', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('disease', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($disease)
            )
            ->add(
                $builder
                    ->create('diseaseheart', 'choice', array(
                        'required' => false,
                        'choices' => $choices['heart']
                    ))
                    ->addModelTransformer($heart)
            )
            ->add(
                $builder
                    ->create('daycare', 'choice', array(
                        'required' => false,
                        'choices' => $choices['daycare']
                    ))
                    ->addModelTransformer($daycare)
            )
            ->add(
                $builder
                    ->create('methodchange', 'choice', array(
                        'required' => false,
                        'choices' => $choices['change']
                    ))
                    ->addModelTransformer($change)
            )
            ->add(
                $builder
                    ->create('schooltype', 'choice', array(
                        'required' => false,
                        'choices' => $choices['school']
                    ))
                    ->addModelTransformer($school)
            )
            ->add('ChildSport', 'text', array(
                'required' => false,
                )
            )
            ->add('ChildHobby', 'text', array(
                'required' => false,
                )
            )
            ->add(
                $builder
                    ->create('status', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('planned', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('complicated', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('Episiotomie', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('PathologyPregnancy', 'text', array(
                'required' => false,
                )
            )
            ->add('PathologyBaby', 'text', array(
                'required' => false,
                )
            )
            ->add('PregnancySymptoms', 'text', array(
                'required' => false,
                )
            )
            ->add('complications', 'text', array(
                'required' => false,
            ))
            ->add('more', 'text', array(
                'required' => false,
                'mapped' => false,
                'data' => 'oui',
            ));

/*
        $family = $this->entityManager->getRepository('MommyProfilBundle:Family')->findOneByUser($this->entityManager->getReference('MommySecurityBundle:User', $session->get('uid')));
        $child = $this->entityManager->getRepository('MommyProfilBundle:Child')->findOneByUser($this->entityManager->getReference('MommySecurityBundle:User', $session->get('uid'))); //$this->entityManager->getRepository('MommySecurityBundle:User')->find($session->get('uid')));
        $size = $this->entityManager->getRepository('MommyProfilBundle:Child')->createQueryBuilder('l')->select('COUNT(l)')->where('l.user = :uid')->setParameter('uid', $session->get('uid'))->getQuery()->getSingleScalarResult();
        if (!is_null($child) && !is_null($family)) {
            if (($family->getSize()->getName() == 'nbenfants-plus') && ($size > 3)) {
                $builder->add('more', 'text', array(
                    'required' => false,
                    'mapped' => false,
                ));
            } else if (($family->getSize()->getName() == 'nbenfants-triples') && ($size > 2)) {
                $builder->add('more', 'text', array(
                    'required' => false,
                    'mapped' => false,
                ));
            } else if (($family->getSize()->getName() == 'nbenfants-jumeaux') && ($size > 1)) {
                $builder->add('more', 'text', array(
                    'required' => false,
                    'mapped' => false,
                ));
            }
        }
*/
        $choices = null; $family = null; $child = null;
        gc_collect_cycles();
    }

    public function getName() {
        return 'enfant';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'enfant',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Child',
         ));
    }

}
