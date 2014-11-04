<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\AdoptApprovalDenial;

class LoadAdoptApprovalDenialData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $adopt_approval_denials = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/adopt_approval_denial.csv"));
        foreach ($adopt_approval_denials as $adopt_approval_denial) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:AdoptApprovalDenial")->findOneByName($adopt_approval_denial[0])) !== null) continue;
            $entity = new AdoptApprovalDenial();

            $entity->setName($adopt_approval_denial[0]);
            $entity->setDescFR($adopt_approval_denial[1]);
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