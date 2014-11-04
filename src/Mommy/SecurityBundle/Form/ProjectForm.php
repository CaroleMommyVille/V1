<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\PregnancyFoodToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\ChildBreastfedToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\DeliveryMethodToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MaternityFoundToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\MaternityToNameTransformer;

use Doctrine\ORM\EntityManager;

class ProjectForm extends AbstractType
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
        $maternity = $this->entityManager->getRepository('MommyProfilBundle:Maternity')->findAll();
        foreach ($maternity as $m) {
            $choices['maternity'][$m->getName()] = $m->getName()."<br/><span class='hint'>".$m->getAddress()->getLiteral()."</span>";
        }
        $maternity = null;

        $bool = new BooleanToNameTransformer($this->entityManager);
        $method = new DeliveryMethodToNameTransformer($this->entityManager);
        $breastfed = new ChildBreastfedToNameTransformer($this->entityManager);
        $food = new PregnancyFoodToNameTransformer($this->entityManager);
        $found = new MaternityFoundToNameTransformer($this->entityManager);
        $maternity = new MaternityToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('maternityfound', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($found)
            )
            ->add(
                $builder
                    ->create('maternity', 'choice', array(
                        'required' => false,
                        'choices' => $choices['maternity'],
                        'attr' => array(
                            'placeholder' => 'Choisissez votre maternité',
                            ),
                    ))
                    ->addModelTransformer($maternity)
            )
            ->add('preparation', 'text', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('method', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($method)
            )
            ->add(
                $builder
                    ->create('breastfed', 'hidden', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($breastfed)
            )
            ->add(
                $builder
                    ->create('food', 'text', array(
                        'required' => false,
                    ))
                    ->addModelTransformer($food)
            );
    }

    public function getName() {
        return 'projet';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'projet',
            'data_class'      => 'Mommy\ProfilBundle\Entity\BirthPlan',
        ));
    }

}