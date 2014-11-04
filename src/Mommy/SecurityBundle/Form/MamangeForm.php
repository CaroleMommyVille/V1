<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\MamangeAgeToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeCaseToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeIVGToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeIMGToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeDiseaseToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeLifeToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeCoupleToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MamangeBabyToNameTransformer;

use Doctrine\ORM\EntityManager;

class MamangeForm extends AbstractType
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
        
        $choices['age'] = $mem->get('profil-form-mamange-age');
        if (!$choices['age']) {
            $age = $mem->get('profil-mamange-age');
            if (is_null($age) || !$age) {
                $age = $this->entityManager->getRepository('MommyProfilBundle:MamangeAge')->findAll();
                $mem->set('profil-mamange-age', $age);
            }
            foreach ($age as $item) {
                $choices['age'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-age', $choices['age'], 0);
        }
        $choices['case'] = $mem->get('profil-form-mamange-case');
        if (!$choices['case']) {
            $case = $mem->get('profil-mamange-case');
            if (is_null($case) || !$case) {
                $case = $this->entityManager->getRepository('MommyProfilBundle:MamangeCase')->findAll();
                $mem->set('profil-mamange-case', $case);
            }
            foreach ($case as $item) {
                $choices['case'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-case', $choices['case'], 0);
        }
        $choices['vecu'] = $mem->get('profil-form-mamange-vecu');
        if (!$choices['vecu']) {
            $vecu = $mem->get('profil-mamange-life');
            if (is_null($vecu) || !$vecu) {
                $vecu = $this->entityManager->getRepository('MommyProfilBundle:MamangeLife')->findAll();
                $mem->set('profil-mamange-life', $vecu);
            }
            foreach ($vecu as $item) {
                $choices['vecu'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-vecu', $choices['vecu'], 0);
        }
        $choices['couple'] = $mem->get('profil-form-mamange-couple');
        if (!$choices['couple']) {
            $couple = $mem->get('profil-mamange-couple');
            if (is_null($couple) || !$couple) {
                $couple = $this->entityManager->getRepository('MommyProfilBundle:MamangeCouple')->findAll();
                $mem->set('profil-mamange-couple', $couple);
            }
            foreach ($couple as $item) {
                $choices['couple'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-couple', $choices['couple'], 0);
        }
        $choices['baby'] = $mem->get('profil-form-mamange-bebe');
        if (!$choices['baby']) {
            $bebe = $mem->get('profil-mamange-baby');
            if (is_null($bebe) || !$bebe) {
                $bebe = $this->entityManager->getRepository('MommyProfilBundle:MamangeBaby')->findAll();
                $mem->set('profil-mamange-baby', $bebe);
            }
            foreach ($bebe as $item) {
                $choices['baby'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-bebe', $choices['baby'], 0);
        }
        $mem = null; unset($mem);

        $age = new MamangeAgeToNameTransformer($this->entityManager);
        $case = new MamangeCaseToNameTransformer($this->entityManager);
        $ivg = new MamangeIVGToNameTransformer($this->entityManager);
        $img = new MamangeIMGToNameTransformer($this->entityManager);
        $disease = new MamangeDiseaseToNameTransformer($this->entityManager);
        $life = new MamangeLifeToNameTransformer($this->entityManager);
        $couple = new MamangeCoupleToNameTransformer($this->entityManager);
        $baby = new MamangeBabyToNameTransformer($this->entityManager);

        $builder
            ->add('since', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-without-day-mamange with-datepicker',
                    'data-calendar' => 'false',
                    'readonly' => 'readonly',
                )
            ))
            ->add(
                $builder
                    ->create('age', 'choice', array(
                        'required' => false,
                        'choices' => $choices['age']
                    ))
                    ->addModelTransformer($age)
            )
            ->add(
                $builder
                    ->create('ivg', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($ivg)
            )
            ->add(
                $builder
                    ->create('img', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($img)
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
                    ->create('case', 'choice', array(
                        'required' => false,
                        'choices' => $choices['case']
                    ))
                    ->addModelTransformer($case)
            )
            ->add(
                $builder
                    ->create('life', 'choice', array(
                        'required' => false,
                        'choices' => $choices['vecu']
                    ))
                    ->addModelTransformer($life)
            )
            ->add('FollowUp', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('couple', 'choice', array(
                        'required' => false,
                        'choices' => $choices['couple']
                    ))
                    ->addModelTransformer($couple)
            )
            ->add(
                $builder
                    ->create('baby', 'choice', array(
                        'required' => false,
                        'choices' => $choices['baby']
                    ))
                    ->addModelTransformer($baby)
            );
    }

    public function getName() {
        return 'mamange';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'mamange',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Mamange',
        ));
    }

}