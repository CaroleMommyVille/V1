<?php

namespace Mommy\ClubBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ClubBundle\Entity\Club;

class LoadClubData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $this->container =& $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //$csv = @file_get_contents(dirname(__FILE__)."/../../Resources/config/club.csv");
        $clubs = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/club.csv"));
        foreach ($clubs as $club) {
            if (($this->container->get('doctrine')->getRepository("MommyClubBundle:Club")->findOneByName($club[0])) !== null) continue;
            $entity = new Club();

            $entity->setName($club[0]);
            $entity->setDescFR($club[1]);
            $entity->setPhoto($club[2]);
            $entity->setEnabled(true);
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