<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildHobby;

use \Memcached;

class LoadHobbyData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_hobbies = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_hobby.csv"));
        foreach ($child_hobbies as $child_hobby) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildHobby")->findOneByName($child_hobby[0])) !== null) continue;
            $entity = new ChildHobby();

            $entity->setName($child_hobby[0]);
            $entity->setDescFR($child_hobby[1]);
            $entity->setEnabled($child_hobby[2]);
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