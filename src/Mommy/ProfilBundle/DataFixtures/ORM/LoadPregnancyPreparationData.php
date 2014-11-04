<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyPreparation;

class LoadPregnancyPreparationData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_preparations = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_preparation.csv"));
        foreach ($pregnancy_preparations as $pregnancy_preparation) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyPreparation")->findOneByName($pregnancy_preparation[0])) !== null) continue;
            $entity = new PregnancyPreparation();

            $entity->setName($pregnancy_preparation[0]);
            $entity->setDescFR($pregnancy_preparation[1]);
            $entity->setEnabled($pregnancy_preparation[2]);
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