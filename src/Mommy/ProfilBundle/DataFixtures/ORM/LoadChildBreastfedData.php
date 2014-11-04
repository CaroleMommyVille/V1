<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\ChildBreastfed;

class LoadChildBreastfedData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $child_breastfeds = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/child_breastfed.csv"));
        foreach ($child_breastfeds as $child_breastfed) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:ChildBreastfed")->findOneByName($child_breastfed[0])) !== null) continue;
            $entity = new ChildBreastfed();

            $entity->setName($child_breastfed[0]);
            $entity->setDescFR($child_breastfed[1]);
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