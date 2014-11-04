<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\RiskFactor;

class LoadRiskFactorData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $risk_factors = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/risk_factor.csv"));
        foreach ($risk_factors as $risk_factor) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:RiskFactor")->findOneByName($risk_factor[0])) !== null) continue;
            $entity = new RiskFactor();

            $entity->setName($risk_factor[0]);
            $entity->setDescFR($risk_factor[1]);
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