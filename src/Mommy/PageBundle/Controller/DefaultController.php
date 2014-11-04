<?php

namespace Mommy\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Form
use Mommy\PageBundle\Form\MessageForm;
use Mommy\PageBundle\Form\CarouselForm;

// User
use Mommy\SecurityBundle\Entity\User;

// Map
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Helper\MapHelper;

// GeoCoding
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\GeocoderProvider;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMapsProvider;

// Marker
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;

// Page
use Mommy\PageBundle\Entity\Like;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/voir/{pid}/display.json", name="page-pid-display-json", requirements={"pid"=".+"})
   */
  public function voirDisplayAction($pid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'index',
        'html' => "/page/voir/$pid",
        'title' => 'MommyPage',
        'menu' => 'refresh',
        'notification' => 'refresh',
        'empty' => true,
      ),
      "1" => array(
        'frame' => '#left',
        'name' => 'search',
        'html' => '/recherche/',
        'empty' => true,
      ),
      "2" => array(
        'frame' => '#left',
        'name' => 'schedule',
        'html' => "/page/horaires/$pid",
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'map',
        'html' => "/page/localisation/$pid",
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/horaires/{pid}", name="page-horaires", requirements={"pid" = ".+"})
   * @Template
   */
  public function horairesAction($pid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $pro = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->find($pid);
    $sched = array(
    	'mon' => array(),
    );
    $days = array(
    	'mon' => array('mon'),
    );
    if (!is_null($pro->getOpening())) {
	    foreach ($pro->getOpening() as $day => $opening) {
	    	$found = false;
	    	if (isset($sched[$day]) && !sizeof($sched[$day])) {
	    		$sched[$day] = $opening;
	    	} else {
	    		foreach ($sched as $d => $s) {
	    			if ($s = $opening) {
	    				$found = true;
	    				$days[$d][] = $day;
	    				break;
	    			}
	    		}
	    	}
	    	if (!$found) {
	    		$sched[$day] = $opening;
	    		$days[$day] = array($day);
	    	}
	    }
	}
    return array(
    	'sched' => $sched,
    	'days' => $days,
    	'text' => $pro->getTimeAsText(),
      );
  }

  	/**
  	 * @Route("/localisation/{pid}", name="page-localisation", requirements={"pid" = ".+"})
  	 * @Template
  	 */
	public function localisationAction($pid) {
	    MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $pro = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->find($pid);

        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
        $geocoder = new \Geocoder\Geocoder();
        $chain    = new \Geocoder\Provider\ChainProvider(array(
            new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
            new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
            ));
        $geocoder->registerProvider($chain);

		$marker = new Marker();
		// Configure your marker options
		$marker->setPrefixJavascriptVariable('marker_');
		$marker->setPosition($pro->getAddress()->getLatitude(), $pro->getAddress()->getLongitude(), true);
		$marker->setAnimation(Animation::DROP);
		$marker->setOptions(array(
		    'clickable' => false,
		    'flat'      => true,
		));

	    $map = new Map();
		$map->setPrefixJavascriptVariable('map_');
		$map->setHtmlContainerId('map_canvas');
		$map->setAsync(true);
		$map->setAutoZoom(true);
		$map->setCenter($pro->getAddress()->getLatitude(), $pro->getAddress()->getLongitude(), true);
		$map->setMapOption('zoom', 3);
		$map->addMarker($marker);
		$map->setMapOption('mapTypeId', MapTypeId::HYBRID);
		$map->setMapOption('mapTypeId', 'hybride');
		$map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
		$map->setMapOption('mapTypeId', 'circulation');
		$map->setMapOption('mapTypeId', MapTypeId::SATELLITE);
		$map->setMapOption('mapTypeId', 'satellite');
		$map->setMapOption('mapTypeId', MapTypeId::TERRAIN);
		$map->setMapOption('mapTypeId', 'terrain');
		$map->setMapOptions(array(
		    'disableDefaultUI'       => true,
		    'disableDoubleClickZoom' => true,
		));
		$map->setStylesheetOptions(array(
		    'width'  => '240px',
		    'height' => '240px',
		));
		$map->setLanguage('fr');

		return array(
			'map' => $map,
			'literal' => $pro->getAddress()->getLiteral()
			);
	}

  	/**
  	 * @Route("/like/{pid}", name="page-like-page", requirements={"pid" = ".+"})
  	 * @Template
  	 */
	public function likeAction($pid) {
	    MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $pro = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->find($pid);
	    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());

	    if (($like = $this->getDoctrine()->getRepository('MommyPageBundle:Like')->findOneBy(array('user' => $user, 'page' => $pro))) === null) {
	    	$like = new Like();
	    	$like->setPage($pro);
	    	$like->setUser($user);
		    $em = $this->getDoctrine()->getManager();
		    $em->persist($like);
		    $em->flush();
		    $em->clear();
	    }
	    $like = $this->getDoctrine()->getRepository('MommyPageBundle:Like')
	        ->createQueryBuilder('l')
	        ->select('count(l.id)')
	        ->where('l.page = :pro')
	        ->setParameters(array(
	          'pro' => $pro->getId(),
	          )
	        )
	        ->getQuery()
	        ->getSingleScalarResult();
	    return array('like' => $like);
	}

  	/**
  	 * @Route("/self", name="page-self")
  	 * @Template
  	 */
	public function selfAction() {
	    MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
	    $pros = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->findByUser($user);

	    return array(
	    	'pros' => $pros,
	    	);
	}

  	/**
  	 * @Route("/recherche/{act}", name="page-search", requirements={"act"=".+"})
  	 * @Template
  	 */
	public function searchAction($act) {
	    MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');

	    $activity = $this->getDoctrine()->getRepository('MommyProfilBundle:JobActivities')->findOneByName($act);
	    $pros = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->findByActivity($activity);

	    return array(
	    	'pros' => $pros
	    	);
	}

  	/**
  	 * @Route("/voir/{pid}", name="page", requirements={"pid" = ".+"})
  	 * @Template
  	 */
	public function indexAction($pid) {
	    MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

		$user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
	    $pro = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->find($pid);
	    $carousel = $this->getDoctrine()->getRepository('MommyProfilBundle:Carousel')->findOneByPro($pro);
	    $manege = $this->getDoctrine()->getRepository('MommyProfilBundle:CarouselPhoto')->findOneByCarousel($carousel);

	    $messages = $this->getDoctrine()->getRepository('MommyPageBundle:Message')->findByWall($pro);
	    arsort($messages);
	    $messages = array_slice($messages, 0, 10, true);

	    $like = $this->getDoctrine()->getRepository('MommyPageBundle:Like')->findByPage($pro);

	    $likes = array();
	    $comments = array();
	    foreach ($messages as $message) {
	      $comments[$message->getId()] = $this->getDoctrine()->getRepository('MommyPageBundle:Comment')->findBy(array('message' => $message), array('date' => 'ASC'));
	      $likes[$message->getId()] = $this->getDoctrine()->getRepository('MommyPageBundle:LikeMessage')
	        ->createQueryBuilder('l')
	        ->select('count(l.id)')
	        ->where('l.message = :message')
	        ->setParameters(array(
	          'message' => $message->getId(),
	          )
	        )
	        ->getQuery()
	        ->getSingleScalarResult();
	    }

	    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));
	    $forms = array(
	      'carousel' => $this->createForm(new CarouselForm($this->getDoctrine()->getManager(), $this->container))->createView(),
	      );
	    $like = $this->getDoctrine()->getRepository('MommyPageBundle:Like')
	        ->createQueryBuilder('l')
	        ->select('count(l.id)')
	        ->where('l.page = :pro')
	        ->setParameters(array(
	          'pro' => $pro->getId(),
	          )
	        )
	        ->getQuery()
	        ->getSingleScalarResult();
	        
	    return array(
	    	'pro' => $pro,
	    	'manege' => $manege,
		    'form' => $form->createView(),
		    'messages' => $messages,
		    'like' => $like,
		    'likes' => $likes,
		    'comments' => $comments,
		    'manege' => $manege,
		    'action' => "/page/post/$pid",
		    'page_like' => "$pid",
		    'page_like_nb' => $like,
	    	);
	}

  /**
   * @Route("/albums", name="page-albums")
   * @Template
   */
  public function albumSumAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    return array();
  }
}