<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildSchoolType;

class LoadChildSchoolTypeData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_school_types = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_school_type.csv"));
        foreach ($child_school_types as $child_school_type) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildSchoolType")->findOneByName($child_school_type[0])) !== null) continue;
            $entity = new ChildSchoolType();

            $entity->setName($child_school_type[0]);
            $entity->setDescFR($child_school_type[1]);
            $entity->setEnabled($child_school_type[2]);
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