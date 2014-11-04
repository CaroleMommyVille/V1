<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\MamangeIMG;

class LoadMamangeIMGData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $mamange_imgs = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/mamange_img.csv"));
        foreach ($mamange_imgs as $mamange_img) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:MamangeIMG")->findOneByName($mamange_img[0])) !== null) continue;
            $entity = new MamangeIMG();

            $entity->setName($mamange_img[0]);
            $entity->setDescFR($mamange_img[1]);
            $entity->setEnabled($mamange_img[2]);
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