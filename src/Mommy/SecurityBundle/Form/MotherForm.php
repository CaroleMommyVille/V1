<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildBreastfedToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AfterDeliveryToNameTransformer;

use Doctrine\ORM\EntityManager;

class MotherForm extends AbstractType
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
        
        $choices['breastfed'] = $mem->get('profil-form-mother-breastfed');
        if (!$choices['breastfed']) {
            $breastfed = $mem->get('profil-child-breastfed');
            if (is_null($breastfed) || !$breastfed) {
                $breastfed = $this->entityManager->getRepository('MommyProfilBundle:ChildBreastfed')->findAll();
                $mem->set('profil-child-breastfed', $breastfed);
            }
            foreach ($breastfed as $item) {
                if ($item->getName() == 'breastfed-essai') continue;
                $choices['breastfed'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mother-breastfed', $choices['breastfed'], 0);
        }
        $mem = null; unset($mem);

        $bool = new BooleanToNameTransformer($this->entityManager);
        $breastfed = new ChildBreastfedToNameTransformer($this->entityManager);
        $after = new AfterDeliveryToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('breastfed', 'choice', array(
                        'required' => false,
                        'choices' => $choices['breastfed'],
                    ))
                    ->addModelTransformer($breastfed)
            )
            ->add('during', 'choice', array(
                'required' => false,
                'choices' => array(
                    '6 mois', '7 mois', '8 mois', '9 mois', '10 mois', '11 mois', '12 mois', 
                    '1 an et demi','deux ans','deux ans et demi'
                    ),
            ))
            ->add(
                $builder
                    ->create('babyblues', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('weightok', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('weighttime', 'choice', array(
                'required' => false,
                'choices' => range(1, 24),
            ))
            ->add(
                $builder
                    ->create('after', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($after)
            );
        $session = $this->container->get('request')->getSession();
        if ($session->get('delivery_date'))
            $builder
            ->add('between', 'choice', array(
                'required' => false,
                'choices' => array(
                    '6 mois', '7 mois', '8 mois', '9 mois', '10 mois', '11 mois', '12 mois', 
                    '1 an et demi','deux ans','deux ans et demi','trois ans','trois ans et demi','quatre ans','quatre ans et demi','cinq ans','5 ans et demi','6 ans et plus', '7 ans', '7 ans et demi', '8 ans et plus'
                    ),
            ));
    }

    public function getName() {
        return 'maman';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'maman',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Mother',
        ));
    }

}