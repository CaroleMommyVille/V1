<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeLife;

class LoadMamangeLifeData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_lifes = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_life.csv"));
        foreach ($mamange_lifes as $mamange_life) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeLife")->findOneByName($mamange_life[0])) !== null) continue;
            $entity = new MamangeLife();

            $entity->setName($mamange_life[0]);
            $entity->setDescFR($mamange_life[1]);
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