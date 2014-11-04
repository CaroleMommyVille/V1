<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeCouple;

class LoadMamangeCoupleData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_couples = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_couple.csv"));
        foreach ($mamange_couples as $mamange_couple) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeCouple")->findOneByName($mamange_couple[0])) !== null) continue;
            $entity = new MamangeCouple();

            $entity->setName($mamange_couple[0]);
            $entity->setDescFR($mamange_couple[1]);
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