<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeAge;

class LoadMamangeAgeData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_ages = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_age.csv"));
        foreach ($mamange_ages as $mamange_age) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeAge")->findOneByName($mamange_age[0])) !== null) continue;
            $entity = new MamangeAge();

            $entity->setName($mamange_age[0]);
            $entity->setDescFR($mamange_age[1]);
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