<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\AdoptAnotherWay;

class LoadAdoptAnotherWayData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $adopt_another_ways = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/adopt_another_way.csv"));
        foreach ($adopt_another_ways as $adopt_another_way) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:AdoptAnotherWay")->findOneByName($adopt_another_way[0])) !== null) continue;
            $entity = new AdoptAnotherWay();

            $entity->setName($adopt_another_way[0]);
            $entity->setDescFR($adopt_another_way[1]);
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