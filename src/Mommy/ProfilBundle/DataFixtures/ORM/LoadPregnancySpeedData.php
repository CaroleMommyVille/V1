<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancySpeed;

class LoadPregnancySpeedData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_speeds = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_speed.csv"));
        foreach ($pregnancy_speeds as $pregnancy_speed) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancySpeed")->findOneByName($pregnancy_speed[0])) !== null) continue;
            $entity = new PregnancySpeed();

            $entity->setName($pregnancy_speed[0]);
            $entity->setDescFR($pregnancy_speed[1]);
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