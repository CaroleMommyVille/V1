<?php

namespace Mommy\UIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Mommy\UIBundle\Entity\Statistics;
use Mommy\UIBundle\Entity\ExternalLink;

use Mommy\UIBundle\MommyUIBundle;

class DefaultController extends Controller
{
    static public $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');

    /**
     * @Route("/current.json", name="ui-current.json")
     * @Template
     */
    public function currentAction() {
        self::logStatistics($this->get('request'));
        $current = array(
            'auth' => false,
        );
        if ($this->container->get('security.context')->getToken()->getUser() !== 'anon.') { 
            $current['auth'] = true;
        }
        return new JsonResponse($current);
    } 

    /**
     * @Route("/menu.json", name="ui-menu.json")
     * @Template
     */
    public function menuAction() {
        self::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if (!$session->get('_is_authenticated')) { 
            $menu = array(
                'MV' => $this->generateUrl('login'),
            );
        } else {
        	$menu = array(
        		'MV' => $this->generateUrl('login'),
        		'MommyProfil' => $this->generateUrl('profil-voir'),
        		'MommyClub' => $this->generateUrl('club-index'),
//        		'MommyMap' => $this->generateUrl('map-index'),
//        		'MommyBlog' => '/blog',
//        		'MommyPlay' => '/play',
//        		'MommyShop' => '',
//        		'MommyBox' => '',
        	);
        }
        return new JsonResponse($menu);
    }

    static public function stripAccents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );
        $string = strtr($string, $chars);
        return $string;
    }

    static public function logStatistics($request) {
        return;
        
        if (!preg_match('/^\/([^\/]+)\//', $request->server->get('REQUEST_URI'), $uri)) $uri = array('', '');
        $uri = trim($uri[1]);
        $uri = strtolower($uri);
        $manager = MommyUIBundle::getContainer()->get('doctrine')->getManager('default');
        $manager->getConnection()->getConfiguration()->setSQLLogger(null);

//        $mem = new \Memcached(MommyUIBundle::getContainer()->getParameter('cache_domain'));
//        $mem->addServer(MommyUIBundle::getContainer()->getParameter('cache_server'), MommyUIBundle::getContainer()->getParameter('cache_port'));
        $mem = $this->get('session');
        
        $time = $mem->get('ui-statistics-'.$uri.'-time');

        $increased = false;

        if ($time == date('YmdH')) {
            $value = $mem->get('ui-statistics-'.$uri);
            $mem->set('ui-statistics-'.$uri, ++$value);
            $statistics = MommyUIBundle::getContainer()->get('doctrine')->getRepository('MommyUIBundle:Statistics')->findOneBy(array('uri' => $uri, 'time' => $time));
            if (!is_null($statistics)) {
                $statistics->setCount($value);
                $manager->persist($statistics);
                $manager->flush();
                $manager->clear();
                $increased = true;
            }
        }
        if (!$increased) {
            $time = date('YmdH');
            $statistics = new Statistics();
            $statistics->setTime($time);
            $statistics->setURI($uri);
            $statistics->setCount(1);
            if ($request->isXmlHttpRequest()) 
                $statistics->setCall('json');
            else
                $statistics->setCall('html');
            $manager->persist($statistics);
            $manager->flush();
            $manager->clear();
            $mem->set('ui-statistics-'.$uri, 1);
            $mem->set('ui-statistics-'.$uri.'-time', $time);
        }

        $mem = null; unset($mem);
    }

    /**
     * @Route("/lien/{id}/image", name="ui-add-image2link", requirements={"id"=".+"})
     * @Template
     */
    public function addImage2LinkAction($id) {
        self::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if (($user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId())) === null) 
            return new Response("Vous ne pouvez rajouter un lien", Response::HTTP_FORBIDDEN);
        if (($link = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($id)) === null)
            return new Response("Vous ne pouvez rajouter un lien", Response::HTTP_FORBIDDEN);

        $link->setImage($request->request->get('image'));
        $em = $this->getDoctrine()->getManager();
        $em->merge($link);
        $em->flush();
        $em->clear();

        return new JsonResponse(array(
            'id' => $link->getId(),
            'image' => $link->getImage(),
            )
        );
    }

    /**
     * @Route("/lien/ajouter", name="ui-add-link")
     * @Template
     */
    public function addLinkAction() {
        self::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if (($user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId())) === null) 
            return new Response("Vous ne pouvez rajouter un lien", Response::HTTP_FORBIDDEN);
        if (($link = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->findOneByUrl($request->request->get('url'))) === null) {
            $link = new ExternalLink();
            $link->setUrl($request->request->get('url'));
            $link->setTitle($request->request->get('title'));
            $link->setText($request->request->get('description'));
            $link->setVideo($request->request->get('video'));
            $link->setPublished();

            $em = $this->getDoctrine()->getManager();
            $em->merge($link);
            $em->flush();
            $em->clear();
        }
        $link = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->findOneByUrl($request->request->get('url'));

        return new JsonResponse(array(
            'id' => $link->getId(),
            'title' => $link->getTitle(),
            'description' => $link->getText(),
            'video' => $link->getVideo(),
            'url' => $link->getUrl(),
            )
        );
    }
}