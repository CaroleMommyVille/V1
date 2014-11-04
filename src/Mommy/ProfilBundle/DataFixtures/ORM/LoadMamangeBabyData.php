<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeBaby;

class LoadMamangeBabyData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_babies = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_baby.csv"));
        foreach ($mamange_babies as $mamange_baby) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeBaby")->findOneByName($mamange_baby[0])) !== null) continue;
            $entity = new MamangeBaby();

            $entity->setName($mamange_baby[0]);
            $entity->setDescFR($mamange_baby[1]);
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