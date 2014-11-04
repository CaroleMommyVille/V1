<?php

namespace Mommy\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mommy\StatsBundle\MommyStatsBundle;

use Mommy\StatsBundle\Entity\UserStats;
use Mommy\StatsBundle\Entity\ProStats;

class UsersController extends Controller
{
    static public function logNewUser() {
        $manager = MommyStatsBundle::getContainer()->get('doctrine')->getManager('default');
        $manager->getConnection()->getConfiguration()->setSQLLogger(null);

//        $mem = new \Memcached(MommyStatsBundle::getContainer()->getParameter('cache_domain'));
//        $mem->addServer(MommyStatsBundle::getContainer()->getParameter('cache_server'), MommyStatsBundle::getContainer()->getParameter('cache_port'));
        $mem = MommyStatsBundle::getContainer()->get('session');
        
        $count = $mem->get('stats-users-'.date('YmdH'));
        $stats = null;
        if (is_null($count)) {
        	$stats = MommyStatsBundle::getContainer()->get('doctrine')->getRepository('MommyStatsBundle:UserStats')->findOneByTime(date('YmdH'));
        	$count = (is_null($stats) ? 0 : $stats->getCount());
        }
        $count++;
        $mem->set('stats-users-'.date('YmdH'), $count);
        if (is_null($stats)) $stats = new UserStats();
        $stats->setTime(date('YmdH'));
        $stats->setCount($count);
		$manager->persist($stats);
		$manager->flush();
		$manager->clear();

        $mem = null; unset($mem);
    }

    static public function logNewPro() {
        $manager = MommyStatsBundle::getContainer()->get('doctrine')->getManager('default');
        $manager->getConnection()->getConfiguration()->setSQLLogger(null);

//        $mem = new \Memcached(MommyStatsBundle::getContainer()->getParameter('cache_domain'));
//        $mem->addServer(MommyStatsBundle::getContainer()->getParameter('cache_server'), MommyStatsBundle::getContainer()->getParameter('cache_port'));
        $mem = MommyStatsBundle::getContainer()->get('session');
        
        $count = $mem->get('stats-users-'.date('YmdH'));
        $stats = null;
        if (is_null($count)) {
        	$stats = MommyStatsBundle::getContainer()->get('doctrine')->getRepository('MommyStatsBundle:ProStats')->findOneByTime(date('YmdH'));
        	$count = (is_null($stats) ? 0 : $stats->getCount());
        }
        $count++;
        $mem->set('stats-users-'.date('YmdH'), $count);
        if (is_null($stats)) $stats = new ProStats();
        $stats->setTime(date('YmdH'));
        $stats->setCount($count);
		$manager->persist($stats);
		$manager->flush();
		$manager->clear();

        $mem = null; unset($mem);
    }
}