<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\Pro;
use Mommy\ProfilBundle\Entity\Address;

class LoadProData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $Pro = new Pro();

//        $csv = @file_get_contents(dirname(__FILE__)."/../../Resources/config/prestataires.csv");
//        $prestataires = explode("\n", $csv);
        $prestataires = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/prestataires.csv"));
        foreach ($prestataires as $prestataire) {
//            $prestataire = str_getcsv($prestataire);
            if (!isset($prestataire[0])) {
var_dump($prestataire);
                echo "manque nom\n";
                continue;
            }
            $Pro->setName(utf8_encode($prestataire[0]));
            if (!isset($prestataire[1])) {
var_dump($prestataire);
                echo "manque address\n";
                continue;
            }
            $addr = utf8_encode($prestataire[1].', '.$prestataire[2].' '.ucwords(strtolower($prestataire[3])).', France');
            if (($address = $manager->getRepository('MommyProfilBundle:Address')->findOneByLiteral($addr)) === null) {
                $address = new Address();
                $address->setLiteral($addr);
                $manager->merge($address);
                $manager->flush();
            }
            $address = $manager->getRepository('MommyProfilBundle:Address')->findOneByLiteral($addr);
            $Pro->setAddress($address);

            $Pro->setPhone($prestataire[4]);
            $Pro->setURL(strtolower($prestataire[6]));
            $Pro->setTimeAsText(utf8_encode($prestataire[7]));
            $Pro->setDescription(utf8_encode($prestataire[8]));
            if (($activity = $manager->getRepository('MommyProfilBundle:JobActivities')->findOneBy(array('desc_fr' => utf8_encode($prestataire[9])), array())) === null) {
var_dump($prestataire);
                echo "activité inconnue: ".$prestataire[9]."\n";
                continue;
            }
            $Pro->setActivity($activity);
            if (($station = $manager->getRepository('MommyMapBundle:Station')->findOneByName(utf8_encode($prestataire[sizeof($prestataire)-3]))) !== null)
                $Pro->setStations($station->getId());

            $manager->merge($Pro);
            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}