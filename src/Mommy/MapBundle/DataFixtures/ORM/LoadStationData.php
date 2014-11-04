<?php

namespace Mommy\MapBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\MapBundle\Entity\Station;

use \Memcached;

class LoadStationData implements FixtureInterface, ContainerAwareInterface
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
        $st = new Station();

        //$osm = @file_get_contents("http://oapi-fr.openstreetmap.fr/oapi/interpreter?data=[out:json];node[%22type:RATP%22~%22metro|rer|tram|bus%22];out;node[%22type:SNCF%22~%22rer|transilien%22];out;%3E;out%20skel;");
        $osm = @file_get_contents(dirname(__FILE__)."/../../Resources/config/transportation.json");
        $stations = json_decode($osm, true);
        foreach ($stations['elements'] as $station) {
            if ($station['type'] == 'node') {
                if (isset($station['tags']['name'])) 
                    $name = $station['tags']['name'];
                elseif (isset($station['tags']['name:RATP']))
                    $name = $station['tags']['name:RATP'];
                elseif (isset($station['tags']['name:fr']))
                    $name = $station['tags']['name:fr'];
                else continue;
                $name = ucfirst($name);
                $exists = $manager->getRepository('MommyMapBundle:Station')->findOneByName($name);
                if (!is_null($exists)) continue;
                $st->setName($name);
                if (isset($station['tags']['type:RATP']))
                    $st->setType(ucfirst($station['tags']['type:RATP']));
                elseif (isset($station['tags']['type:SNCF']))
                    $st->setType(ucfirst($station['tags']['type:SNCF']));
                $st->setLatitude($station['lat']);
                $st->setLongitude($station['lon']);
                $manager->merge($st);
                $manager->flush();
                $manager->clear();
            }
        }
        $csv = @file(dirname(__FILE__)."/../../Resources/config/transilien.csv");
        foreach ($csv as $name) {
            $exists = $manager->getRepository('MommyMapBundle:Station')->findOneByName($name);
            if (!is_null($exists)) continue;
            $st->setName($name);
            $st->setType('Transilien');
            $manager->merge($st);
            $manager->flush();
            $manager->clear();
        }

//        $mem = new Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->container->get('session');
        
        $mem->set('map-station', $manager->getRepository('MommyMapBundle:Station')->findAll(), 0);
        $mem = null; unset($mem);

    }
}