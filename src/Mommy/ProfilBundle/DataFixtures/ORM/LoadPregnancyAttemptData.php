<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancyAttempt;

class LoadPregnancyAttemptData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_attempts = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_attempt.csv"));
        foreach ($pregnancy_attempts as $pregnancy_attempt) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancyAttempt")->findOneByName($pregnancy_attempt[0])) !== null) continue;
            $entity = new PregnancyAttempt();

            $entity->setName($pregnancy_attempt[0]);
            $entity->setDescFR($pregnancy_attempt[1]);
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