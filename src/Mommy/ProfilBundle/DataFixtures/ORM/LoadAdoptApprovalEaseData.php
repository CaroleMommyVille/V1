<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\AdoptApprovalEase;

class LoadAdoptApprovalEaseData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $adopt_approval_eases = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/adopt_approval_ease.csv"));
        foreach ($adopt_approval_eases as $adopt_approval_ease) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:AdoptApprovalEase")->findOneByName($adopt_approval_ease[0])) !== null) continue;
            $entity = new AdoptApprovalEase();

            $entity->setName($adopt_approval_ease[0]);
            $entity->setDescFR($adopt_approval_ease[1]);
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