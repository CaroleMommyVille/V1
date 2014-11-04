<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\DeliveryMethodChange;

class LoadDeliveryMethodChangeData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $delivery_method_changes = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/delivery_method_change.csv"));
        foreach ($delivery_method_changes as $delivery_method_change) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:DeliveryMethodChange")->findOneByName($delivery_method_change[0])) !== null) continue;
            $entity = new DeliveryMethodChange();

            $entity->setName($delivery_method_change[0]);
            $entity->setDescFR($delivery_method_change[1]);
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