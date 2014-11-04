<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyDaddy;

class LoadPregnancyDaddyData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_daddys = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_daddy.csv"));
        foreach ($pregnancy_daddys as $pregnancy_daddy) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyDaddy")->findOneByName($pregnancy_daddy[0])) !== null) continue;
            $entity = new PregnancyDaddy();

            $entity->setName($pregnancy_daddy[0]);
            $entity->setDescFR($pregnancy_daddy[1]);
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