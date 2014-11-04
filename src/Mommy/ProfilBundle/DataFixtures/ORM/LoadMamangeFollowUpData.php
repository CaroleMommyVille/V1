<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeFollowUp;

class LoadMamangeFollowUpData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_follow_ups = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_follow_up.csv"));
        foreach ($mamange_follow_ups as $mamange_follow_up) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeFollowUp")->findOneByName($mamange_follow_up[0])) !== null) continue;
            $entity = new MamangeFollowUp();

            $entity->setName($mamange_follow_up[0]);
            $entity->setDescFR($mamange_follow_up[1]);
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