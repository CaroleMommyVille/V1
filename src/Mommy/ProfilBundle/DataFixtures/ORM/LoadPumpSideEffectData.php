<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PumpSideEffect;

class LoadPumpSideEffectData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pump_side_effects = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pump_side_effect.csv"));
        foreach ($pump_side_effects as $pump_side_effect) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PumpSideEffect")->findOneByName($pump_side_effect[0])) !== null) continue;
            $entity = new PumpSideEffect();

            $entity->setName($pump_side_effect[0]);
            $entity->setDescFR($pump_side_effect[1]);
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