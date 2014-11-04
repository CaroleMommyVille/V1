<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\JobActivitiesCategories;

class LoadJobActivitiesCategoriesData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $job_activities_categoriess = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/job_activities_categories.csv"));
        foreach ($job_activities_categoriess as $job_activities_categories) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:JobActivitiesCategories")->findOneByName($job_activities_categories[0])) !== null) continue;
            $entity = new JobActivitiesCategories();

            $entity->setName($job_activities_categories[0]);
            $entity->setDescFR($job_activities_categories[1]);
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