<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildDaycare;

class LoadChildDaycareData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_daycares = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_daycare.csv"));
        foreach ($child_daycares as $child_daycare) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildDaycare")->findOneByName($child_daycare[0])) !== null) continue;
            $entity = new ChildDaycare();

            $entity->setName($child_daycare[0]);
            $entity->setDescFR($child_daycare[1]);
            $entity->setEnabled($child_daycare[2]);
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