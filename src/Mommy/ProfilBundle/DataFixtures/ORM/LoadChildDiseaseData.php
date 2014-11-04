<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildDisease;

class LoadChildDiseaseData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_diseases = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_disease.csv"));
        foreach ($child_diseases as $child_disease) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildDisease")->findOneByName($child_disease[0])) !== null) continue;
            $entity = new ChildDisease();

            $entity->setName($child_disease[0]);
            $entity->setDescFR($child_disease[1]);
            $entity->setHasDetail($child_disease[2]);
            $entity->setEnabled($child_disease[3]);
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