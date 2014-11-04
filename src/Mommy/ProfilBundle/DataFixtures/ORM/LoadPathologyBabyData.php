<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PathologyBaby;

use \Memcached;

class LoadPathologyBabyData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pathos = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pathology_baby.csv"));
        foreach ($pathos as $patho) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PathologyBaby")->findOneByName($patho[0])) !== null) continue;
            $entity = new PathologyBaby();

            $entity->setName($patho[0]);
            $entity->setDescFR($patho[1]);
            $entity->setHasDetail($patho[2]);
            $entity->setEnabled($patho[3]);
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