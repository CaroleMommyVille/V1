<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancySymptoms;

class LoadPregnancySymptomsData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $pregnancy_symptomss = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_symptoms.csv"));
        foreach ($pregnancy_symptomss as $pregnancy_symptoms) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancySymptoms")->findOneByName($pregnancy_symptoms[0])) !== null) continue;
            $entity = new PregnancySymptoms();

            $entity->setName($pregnancy_symptoms[0]);
            $entity->setDescFR($pregnancy_symptoms[1]);
            $manager->merge($entity);
            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}