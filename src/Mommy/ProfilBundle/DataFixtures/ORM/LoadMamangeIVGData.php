<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeIVG;

class LoadMamangeIVGData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_ivgs = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_ivg.csv"));
        foreach ($mamange_ivgs as $mamange_ivg) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeIVG")->findOneByName($mamange_ivg[0])) !== null) continue;
            $entity = new MamangeIVG();

            $entity->setName($mamange_ivg[0]);
            $entity->setDescFR($mamange_ivg[1]);
            $entity->setEnabled($mamange_ivg[2]);
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