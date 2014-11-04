<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\PregnancySo;

class LoadPregnancySoData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $pregnancy_sos = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/pregnancy_so.csv"));
        foreach ($pregnancy_sos as $pregnancy_so) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:PregnancySo")->findOneByName($pregnancy_so[0])) !== null) continue;
            $entity = new PregnancySo();

            $entity->setName($pregnancy_so[0]);
            $entity->setDescFR($pregnancy_so[1]);
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