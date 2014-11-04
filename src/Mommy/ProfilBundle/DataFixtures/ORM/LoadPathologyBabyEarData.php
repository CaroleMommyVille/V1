<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PathologyBabyEar;

class LoadPathologyBabyEarData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pathos = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pathology_baby_ear.csv"));
        foreach ($pathos as $patho) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PathologyBabyEar")->findOneByName($patho[0])) !== null) continue;
            $entity = new PathologyBabyEar();

            $entity->setName($patho[0]);
            $entity->setDescFR($patho[1]);
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