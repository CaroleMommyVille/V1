<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeCase;

class LoadMamangeCaseData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_cases = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_case.csv"));
        foreach ($mamange_cases as $mamange_case) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeCase")->findOneByName($mamange_case[0])) !== null) continue;
            $entity = new MamangeCase();

            $entity->setName($mamange_case[0]);
            $entity->setDescFR($mamange_case[1]);
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