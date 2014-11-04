<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\PregnancyWayToToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancySoToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyMoodToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyDaddyToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\PregnancyHopeToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;

use Doctrine\ORM\EntityManager;

class AlmostForm extends AbstractType
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
        $way = $this->entityManager->getRepository('MommyProfilBundle:PregnancyWayTo')->findAll();
        foreach ($way as $item) {
            $choices['way'][$item->getName()] = $item->getDescFR();
        }
        $way = null;
        $so = $this->entityManager->getRepository('MommyProfilBundle:PregnancySo')->findAll();
        foreach ($so as $item) {
            $choices['so'][$item->getName()] = $item->getDescFR();
        }
        $so = null;
        $dad = $this->entityManager->getRepository('MommyProfilBundle:PregnancyDaddy')->findAll();
        foreach ($dad as $item) {
            $choices['dad'][$item->getName()] = $item->getDescFR();
        }
        $dad = null;
        $mood = $this->entityManager->getRepository('MommyProfilBundle:PregnancyMood')->findAll();
        foreach ($mood as $item) {
            $choices['mood'][$item->getName()] = $item->getDescFR();
        }
        $mood = null;
        $hope = $this->entityManager->getRepository('MommyProfilBundle:PregnancyHope')->findAll();
        foreach ($hope as $item) {
            $choices['hope'][$item->getName()] = $item->getDescFR();
        }
        $hope = null;

        $wayto = new PregnancyWayToToNameTransformer($this->entityManager);
        $so = new PregnancySoToNameTransformer($this->entityManager);
        $dad = new PregnancyDaddyToNameTransformer($this->entityManager);
        $mood = new PregnancyMoodToNameTransformer($this->entityManager);
        $hope = new PregnancyHopeToNameTransformer($this->entityManager);
        $bool = new BooleanToNameTransformer($this->entityManager);

        $builder
            ->add('since', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-without-day-mamange with-datepicker',
                    'readonly' => 'readonly',
                    'data-calendar' => 'false',
                )
            ))
            ->add(
                $builder
                    ->create('way', 'choice', array(
                        'required' => false,
                        'choices' => $choices['way']
                    ))
                    ->addModelTransformer($wayto)
            )
            ->add(
                $builder
                    ->create('so', 'choice', array(
                        'required' => false,
                        'choices' => $choices['so']
                    ))
                    ->addModelTransformer($so)
            )
            ->add(
                $builder
                    ->create('dad', 'choice', array(
                        'required' => false,
                        'choices' => $choices['dad']
                    ))
                    ->addModelTransformer($dad)
            )
            ->add(
                $builder
                    ->create('mood', 'choice', array(
                        'required' => false,
                        'choices' => $choices['mood']
                    ))
                    ->addModelTransformer($mood)
            )
            ->add(
                $builder
                    ->create('hope', 'choice', array(
                        'required' => false,
                        'choices' => $choices['hope']
                    ))
                    ->addModelTransformer($hope)
            )
            ->add(
                $builder
                    ->create('prems', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            );
    }

    public function getName() {
        return 'presquenceinte';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'presquenceinte',
            'data_class'      => 'Mommy\ProfilBundle\Entity\AlmostPregnant',
        ));
    }

}