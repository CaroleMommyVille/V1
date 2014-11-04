<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\TechPMA;

class LoadTechPMAData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $tech_pmas = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/tech_pma.csv"));
        foreach ($tech_pmas as $tech_pma) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:TechPMA")->findOneByName($tech_pma[0])) !== null) continue;
            $entity = new TechPMA();

            $entity->setName($tech_pma[0]);
            $entity->setDescFR($tech_pma[1]);
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