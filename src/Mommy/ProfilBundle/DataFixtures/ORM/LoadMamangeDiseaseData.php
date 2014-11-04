<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeDisease;

class LoadMamangeDiseaseData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_diseases = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_disease.csv"));
        foreach ($mamange_diseases as $mamange_disease) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeDisease")->findOneByName($mamange_disease[0])) !== null) continue;
            $entity = new MamangeDisease();

            $entity->setName($mamange_disease[0]);
            $entity->setDescFR($mamange_disease[1]);
            $entity->setEnabled($mamange_disease[2]);
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