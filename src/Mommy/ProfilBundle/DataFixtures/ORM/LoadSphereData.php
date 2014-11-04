<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\Sphere;

class LoadSphereData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $spheres = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/sphere.csv"));
        foreach ($spheres as $sphere) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:Sphere")->findOneByName($sphere[1])) !== null) continue;
            $entity = new Sphere();

            $Language = $this->container->get('doctrine')->getRepository("MommyProfilBundle:Language")->findOneByName($sphere[0]);
            $entity->setName($sphere[1]);
            $entity->setDescFR($sphere[2]);
            $entity->setLanguage($Language);
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
        return 2; // the order in which fixtures will be loaded
    }
}