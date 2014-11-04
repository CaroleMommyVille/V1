<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildSport;

class LoadChildSportData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_sports = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_sport.csv"));
        foreach ($child_sports as $child_sport) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildSport")->findOneByName($child_sport[0])) !== null) continue;
            $entity = new ChildSport();

            $entity->setName($child_sport[0]);
            $entity->setDescFR($child_sport[1]);
            $entity->setEnabled($child_sport[2]);
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