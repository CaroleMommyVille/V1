<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MaternityFound;

class LoadMaternityFoundData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $maternity_founds = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/maternity_found.csv"));
        foreach ($maternity_founds as $maternity_found) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MaternityFound")->findOneByName($maternity_found[0])) !== null) continue;
            $entity = new MaternityFound();

            $entity->setName($maternity_found[0]);
            $entity->setDescFR($maternity_found[1]);
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