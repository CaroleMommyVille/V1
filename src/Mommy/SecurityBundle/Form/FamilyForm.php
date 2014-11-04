<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildNewToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\FamilySizeToNameTransformer;

use Doctrine\ORM\EntityManager;

class FamilyForm extends AbstractType
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
        
        $choices['children'] = $mem->get('security-form-famille-children');
        if (!$choices['children']) {
            $children = $mem->get('profil-family-size');
            if (is_null($children) || !$children) {
                $children = $this->entityManager->getRepository('MommyProfilBundle:FamilySize')->findAll();
                $mem->set('profil-family-size', $children);
            }
            foreach ($children as $item) {
                $choices['children'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('security-form-famille-children', $choices['children'], 0);
        }
        $choices['when'] = $mem->get('security-form-famille-when');
        if (!$choices['when']) {
            $when = $mem->get('profil-child-new');
            if (is_null($when) || !$when) {
                $when = $this->entityManager->getRepository('MommyProfilBundle:ChildNew')->findAll();
                $mem->set('profil-child-new', $when);
            }
            foreach ($when as $item) {
                $choices['when'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('security-form-famille-when', $choices['when'], 0);
        }
        $mem = null; unset($mem);

        $bool = new BooleanToNameTransformer($this->entityManager);
        $when = new ChildNewToNameTransformer($this->entityManager);
        $size = new FamilySizeToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('size', 'choice', array(
                        'required' => false,
                        'choices' => $choices['children'],
                    ))
                    ->addModelTransformer($size)
            )
            ->add(
                $builder
                    ->create('new', 'hidden', array(
                        'required' => false,
                        'attr' => array(
                            'value' => ''
                            ),
                    ))
                    ->addModelTransformer($bool)
            )
            ->add(
                $builder
                    ->create('when', 'choice', array(
                        'required' => false,
                        'choices' => $choices['when']
                    ))
                    ->addModelTransformer($when)
            );
    }

    public function getName() {
        return 'famille';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'famille',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Family',
         ));
    }

}