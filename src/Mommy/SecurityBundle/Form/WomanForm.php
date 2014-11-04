<?php

namespace Mommy\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mommy\SecurityBundle\Form\DataTransformer\MarriageToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\AddressToLiteralTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\NoWorkToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\DiplomaToNameTransformer;
use Mommy\SecurityBundle\Form\DataTransformer\BooleanToNameTransformer;

use Doctrine\ORM\EntityManager;

class WomanForm extends AbstractType
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

        $choices['marriage'] = $mem->get('profil-form-woman-marriage');
        if (!$choices['marriage']) {
            $marriage = $mem->get('profil-marriage');
            if (is_null($marriage) || !$marriage) {
                $marriage = $this->entityManager->getRepository('MommyProfilBundle:Marriage')->findAll();
                $mem->set('profil-marriage', $marriage);
            }
            foreach ($marriage as $item) {
                $choices['marriage'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-woman-marriage', $choices['marriage'], 0);
        }
        $choices['nowork'] = $mem->get('profil-form-woman-nowork');
        if (!$choices['nowork']) {
            $nowork = $mem->get('profil-no-work');
            if (is_null($nowork) || !$nowork) {
                $nowork = $this->entityManager->getRepository('MommyProfilBundle:NoWork')->findAll();
                $mem->set('profil-no-work', $nowork);
            }
            foreach ($nowork as $item) {
                $choices['nowork'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-woman-nowork', $choices['nowork'], 0);
        }
        $choices['diplome'] = $mem->get('profil-form-woman-diplome');
        if (!$choices['diplome']) {
            $diplome = $mem->get('profil-diploma');
            if (is_null($diplome) || !$diplome) {
                $diplome = $this->entityManager->getRepository('MommyProfilBundle:Diploma')->findAll();
                $mem->set('profil-diploma', $diplome);
            }
            foreach ($diplome as $item) {
                $choices['diplome'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-woman-diplome', $choices['diplome'], 0);
        }

        $mem = null; unset($mem);

        $marriage = new MarriageToNameTransformer($this->entityManager);
        $address = new AddressToLiteralTransformer($this->entityManager);
        $nowork = new NoWorkToNameTransformer($this->entityManager);
        $diploma = new DiplomaToNameTransformer($this->entityManager);
        $bool = new BooleanToNameTransformer($this->entityManager);

        $builder
            ->add(
                $builder
                    ->create('marriage', 'choice', array(
                        'required' => false,
                        'choices' => $choices['marriage'],
                    ))
                    ->addModelTransformer($marriage)
            )
            ->add('since', 'text', array(
            	'required' => false,
            	'attr' => array(
                    'placeholder' => '',
            		'class' => 'date-without-day with-datepicker',
            		'data-calendar' => 'false',
                    'readonly' => 'readonly',
            	)
            ))
            ->add(
                $builder
                    ->create('address', 'text', array(
                        'required' => false,
                        'attr' => array(
                            'class' => 'address',
                            ),
                    ))
                    ->addModelTransformer($address)
            )
            ->add('Stations', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'station',
                    ),
            ))
            ->add(
                $builder
                    ->create('job', 'hidden', array(
                        'required' => false,
                        'mapped' => false,
                    ))
                    ->addModelTransformer($bool)
            )
            ->add('jobtitle', 'text', array(
            	'required' => false,
            ))
            ->add(
                $builder
                    ->create('jobaddress', 'text', array(
                        'required' => false,
                        'attr' => array(
                            'class' => 'address',
                            ),
                    ))
                    ->addModelTransformer($address)
            )
            ->add('JobStations', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'station',
                    ),
            ))
            ->add(
                $builder
                    ->create('nowork', 'choice', array(
                        'required' => false,
                        'choices' => $choices['nowork'],
                        'empty_value' => 'Pour quelle raison ?',
                    ))
                    ->addModelTransformer($nowork)
            )
            ->add('Languages', 'hidden', array(
                'required' => false,
            ))
            ->add('Spheres', 'hidden', array(
                'required' => false,
            ))
            ->add('style', 'text', array(
                'required' => false,
            ))
            ->add('Sports', 'hidden', array(
                'required' => false,
            ))
            ->add(
                $builder
                    ->create('diploma', 'choice', array(
                        'required' => false,
                        'choices' => $choices['diplome'],
                        'empty_value' => 'Choisissez un diplôme',
                    ))
                    ->addModelTransformer($diploma)
            )
            ->add('Activities', 'hidden', array(
                'required' => false,
            ))
            ->add('Holidays', 'hidden', array(
                'required' => false,
            ))
            ->add('TVShows', 'text', array(
                'required' => false,
            ))
            ->add('Movies', 'text', array(
                'required' => false,
            ))
            ->add('Musics', 'text', array(
                'required' => false,
            ))
            ->add('VIP', 'text', array(
                'required' => false,
            ))
            ->add('withspheres', 'checkbox', array(
                'required' => false,
                'mapped' => false
            ));
    }

    public function getName() {
        return 'femme';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'femme',
            'data_class'      => 'Mommy\ProfilBundle\Entity\Woman',
        ));
    }

}