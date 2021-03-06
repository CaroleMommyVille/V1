<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildDiseaseHeart;

class LoadChildDiseaseHeartData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_disease_hearts = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_disease_heart.csv"));
        foreach ($child_disease_hearts as $child_disease_heart) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildDiseaseHeart")->findOneByName($child_disease_heart[0])) !== null) continue;
            $entity = new ChildDiseaseHeart();

            $entity->setName($child_disease_heart[0]);
            $entity->setDescFR($child_disease_heart[1]);
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