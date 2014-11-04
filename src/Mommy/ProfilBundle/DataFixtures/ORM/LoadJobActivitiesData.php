<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadJobActivitiesData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $job_activitiess = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/job_activities.csv"));
        foreach ($job_activitiess as $job_activities) {
            if (($this->container->get('doctrine')->getRepository("MommyProfilBundle:JobActivities")->findOneByName($job_activities[1])) !== null) continue;
            $entity = new JobActivities();

            $category = $this->container->get('doctrine')->getRepository("MommyProfilBundle:JobActivitiesCategories")->findOneByName($job_activities[0]);
            $entity->setName($job_activities[1]);
            $entity->setDescFR($job_activities[2]);
            $entity->setCategory($category);
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
        return 2; // the order in which fixtures will be loaded
    }
}