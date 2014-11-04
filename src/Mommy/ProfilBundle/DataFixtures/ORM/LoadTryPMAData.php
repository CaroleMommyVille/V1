<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\TryPMA;

class LoadTryPMAData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
     * {@inheritDocTryPMA
     */
    public function load(ObjectManager $manager)
    {
        $try_pmas = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/try_pma.csv"));
        foreach ($try_pmas as $try_pma) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:TryPMA")->findOneByName($try_pma[0])) !== null) continue;
            $entity = new TryPMA();

            $entity->setName($try_pma[0]);
            $entity->setDescFR($try_pma[1]);
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