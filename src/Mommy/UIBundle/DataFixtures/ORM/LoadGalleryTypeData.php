<?php

namespace Mommy\UIBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\UIBundle\Entity\GalleryType;

class LoadGalleryTypeData implements FixtureInterface, ContainerAwareInterface
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
        $gallery_types = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/gallery_type.csv"));
        foreach ($gallery_types as $gallery_type) {
            if (($this->container->get('doctrine')->getRepository("MommyUIBundle:GalleryType")->findOneByName($gallery_type[0])) !== null) continue;
            $entity = new GalleryType();

            $entity->setName($gallery_type[0]);
            $entity->setDescFR($gallery_type[1]);
            $entity->setCover($gallery_type[2]);
            $manager->merge($entity);
            $manager->flush();
            $manager->clear();
        }
    }
}