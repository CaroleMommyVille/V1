<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\JobActivitiesToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AddressToLiteralTransformer;

use Doctrine\ORM\EntityManager;

class ProForm extends AbstractType
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
        
        $choices['activity'] = $mem->get('profil-form-pro');
        if (!$choices['activity']) {
            $activities = $mem->get('profil-job-activities');
            if (is_null($activities) || !$activities) {
                $activities = $this->entityManager->getRepository('MommyProfilBundle:JobActivities')->findAll();
                $mem->set('profil-job-activities', $activities);
            }
            foreach ($activities as $activity) {
                $cat = $this->entityManager->getRepository('MommyProfilBundle:JobActivitiesCategories')->find($activity->getCategory());
                $choices['activity'][$cat->getDescFR()][$activity->getName()] = $activity->getDescFR();
            }
            $mem->set('profil-form-pro', $choices['activity'], 0);
        }
        $mem = null; unset($mem);

        $choices['opening'] = array(
            '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30',
            '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
            '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30',
        );

        $activity = new JobActivitiesToNameTransformer($this->entityManager);
        $address = new AddressToLiteralTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('activity', 'choice', array(
                        'required' => true,
                        'choices' => $choices['activity'],
                        'empty_value' => 'Choisissez une activité',
                    ))
                    ->addModelTransformer($activity)
            )
            ->add('name', 'text', array(
                'required' => true,
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
                    ))
                    ->addModelTransformer($address)
            )
            ->add('stations', 'text', array(
                'required' => true,
                'attr' => array(
                    'class' => 'station',
                ),
            ))
            ->add('phone', 'text', array(
                'required' => true,
                )
            )
            ->add('mondaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('mondaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('mondayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('mondayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'attr' => array(
                    'class' => 'daily',
                    ),
                'empty_value' => '',
                )
            )
            ->add('tuesdaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('tuesdaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('tuesdayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('tuesdayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('wednesdaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('wednesdaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('wednesdayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('wednesdayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('thursdaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('thursdaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('thursdayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('thursdayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('fridaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('fridaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('fridayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('fridayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('saturdaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('saturdaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('saturdayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('saturdayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('sundaymorningstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('sundaymorningend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('sundayafternoonstart', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('sundayafternoonend', 'choice', array(
                'required' => false,
                'choices' => $choices['opening'],
                'empty_value' => '',
                'attr' => array(
                    'class' => 'daily',
                    ),
                )
            )
            ->add('description', 'textarea', array(
                'required' => true,
                'attr' => array(
                    'class' => 'wide',
                    ),
                )
            );
    }

    public function getName() {
        return 'pro';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'pro',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Pro',
        ));
    }

}