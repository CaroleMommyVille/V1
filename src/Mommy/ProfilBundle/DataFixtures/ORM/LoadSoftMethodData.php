<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\SoftMethod;

class LoadSoftMethodData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $soft_methods = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/soft_method.csv"));
        foreach ($soft_methods as $soft_method) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:SoftMethod")->findOneByName($soft_method[0])) !== null) continue;
            $entity = new SoftMethod();

            $entity->setName($soft_method[0]);
            $entity->setDescFR($soft_method[1]);
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