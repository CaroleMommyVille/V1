<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\AfterDelivery;

class LoadAfterDeliveryData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $after_deliveries = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/after_delivery.csv"));
        foreach ($after_deliveries as $after_delivery) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:AfterDelivery")->findOneByName($after_delivery[0])) !== null) continue;
            $entity = new AfterDelivery();

            $entity->setName($after_delivery[0]);
            $entity->setDescFR($after_delivery[1]);
            $entity->setEnabled($after_delivery[2]);
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