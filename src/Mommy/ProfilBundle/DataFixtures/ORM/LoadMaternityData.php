<?php

namespace Mommy\ProfilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mommy\ProfilBundle\Entity\Maternity;
use Mommy\ProfilBundle\Entity\Address;

use \Memcached;

class LoadMaternityData implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
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
        $Maternity = new Maternity();

//        $csv = @file_get_contents(dirname(__FILE__)."/../../Resources/config/maternites.csv");
//        $maternites = explode("\n", $csv);
        $maternites = array_map('str_getcsv', file(dirname(__FILE__)."/../../Resources/config/maternites.csv"));
        foreach ($maternites as $maternite) {
//            $maternite = explode(',', $maternite);
            if (!isset($maternite[0]))
                continue;
            if ($manager->getRepository('MommyProfilBundle:Maternity')->findOneByName(utf8_encode($maternite[0])) !== null) continue;
            $Maternity->setName(utf8_encode($maternite[0]));
            if (!isset($maternite[1]))
                continue;
            $Maternity->setType($maternite[1]);
            $Maternity->setLevel($maternite[2]);

            $addr = utf8_encode($maternite[3].', '.$maternite[4].' '.ucwords(strtolower($maternite[5])).', France');
            if (($address = $manager->getRepository('MommyProfilBundle:Address')->findOneByLiteral($addr)) === null) {
                $address = new Address();
                $address->setLiteral($addr);
                $manager->merge($address);
                $manager->flush();
            }
            $address = $manager->getRepository('MommyProfilBundle:Address')->findOneByLiteral($addr);
            $Maternity->setAddress($address);

            if (strstr($maternite[6], '??') !== false)
                $Maternity->setURL(strtolower($maternite[6]));
            $Maternity->setPhone($maternite[7]);
            if (isset($maternite[8]) && ($maternite[8] != 'NC'))
                $Maternity->setCesarienne(floatval($maternite[8]));
            if (isset($maternite[9]) && ($maternite[9] != 'NC'))
                $Maternity->setPeridurale(floatval($maternite[9]));
            if (isset($maternite[10]) && ($maternite[10] != 'NC'))
                $Maternity->setDuree(floatval($maternite[10]));
            if (isset($maternite[11]) && ($maternite[11] != 'NC'))
                $Maternity->setLit(intval($maternite[11]));
            if (isset($maternite[12]) && ($maternite[12] != 'NC'))
                $Maternity->setAccouchement(intval($maternite[12]));
            if (isset($maternite[13]) && ($maternite[13] != 'NC'))
                $Maternity->setchambre(intval($maternite[13]));
            $manager->merge($Maternity);
            $manager->flush();
            $manager->clear();
        }

//        $mem = new Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->container->get('session');
        
        $mem->set('profil-maternity', $manager->getRepository('MommyProfilBundle:Maternity')->findAll(), 0);
        $mem = null; unset($mem);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}