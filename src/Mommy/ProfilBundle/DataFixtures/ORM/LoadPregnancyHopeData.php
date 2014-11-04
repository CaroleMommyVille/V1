<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyHope;

class LoadPregnancyHopeData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_hopes = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_hope.csv"));
        foreach ($pregnancy_hopes as $pregnancy_hope) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyHope")->findOneByName($pregnancy_hope[0])) !== null) continue;
            $entity = new PregnancyHope();

            $entity->setName($pregnancy_hope[0]);
            $entity->setDescFR($pregnancy_hope[1]);
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