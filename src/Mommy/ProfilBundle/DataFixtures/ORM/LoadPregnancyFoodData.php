<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyFood;

class LoadPregnancyFoodData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_foods = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_food.csv"));
        foreach ($pregnancy_foods as $pregnancy_food) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyFood")->findOneByName($pregnancy_food[0])) !== null) continue;
            $entity = new PregnancyFood();

            $entity->setName($pregnancy_food[0]);
            $entity->setDescFR($pregnancy_food[1]);
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