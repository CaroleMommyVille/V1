<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyStatus;

class LoadPregnancyStatusData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_statuss = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_status.csv"));
        foreach ($pregnancy_statuss as $pregnancy_status) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyStatus")->findOneByName($pregnancy_status[0])) !== null) continue;
            $entity = new PregnancyStatus();

            $entity->setName($pregnancy_status[0]);
            $entity->setDescFR($pregnancy_status[1]);
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