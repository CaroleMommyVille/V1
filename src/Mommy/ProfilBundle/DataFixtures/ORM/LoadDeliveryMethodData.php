<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\DeliveryMethod;

class LoadDeliveryMethodData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $delivery_methods = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/delivery_method.csv"));
        foreach ($delivery_methods as $delivery_method) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:DeliveryMethod")->findOneByName($delivery_method[0])) !== null) continue;
            $entity = new DeliveryMethod();

            $entity->setName($delivery_method[0]);
            $entity->setDescFR($delivery_method[1]);
            $entity->setEnabled($delivery_method[2]);
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