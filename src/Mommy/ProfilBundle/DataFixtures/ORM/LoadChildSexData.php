<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildSex;

class LoadChildSexData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_sexs = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_sex.csv"));
        foreach ($child_sexs as $child_sex) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildSex")->findOneByName($child_sex[0])) !== null) continue;
            $entity = new ChildSex();

            $entity->setName($child_sex[0]);
            $entity->setDescFR($child_sex[1]);
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