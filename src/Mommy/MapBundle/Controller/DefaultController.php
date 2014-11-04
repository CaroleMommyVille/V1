<?php

namespace Mommy\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

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

// Station
use Mommy\MapBundle\Entity\Station;

// Memcached
use \Memcached;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
	/**
	 * @Route("/display.json", name="map-display-json")
	 */
	public function voirDisplayAction() {
	    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
	    $display = array(
	      	"0" => array(
		        'frame' => '#center',
		        'name' => 'index',
		        'html' => "/map",
		        'title' => 'MommyMap',
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
		        'name' => 'my',
		        'html' => "/page/self",
		        'empty' => false,
	      	),
	    );
	    return new JsonResponse($display);
	}

    /**
	 * @Route("/", name="map-index")
     * @Template
     */
    public function indexAction()
    {
		MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
		$session = $request->getSession();
	    $method = $request->getMethod();

		$user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());

		$marker = new Marker();
		// Configure your marker options
		$marker->setPrefixJavascriptVariable('marker_');
		$marker->setPosition($user->getAddress()->getLatitude(), $user->getAddress()->getLongitude(), true);
		$marker->setAnimation(Animation::DROP);
		$marker->setOptions(array(
		    'clickable' => false,
		    'flat'      => true,
		));
		$marker->setIcon('/img/icones/home-20x21.png');

		$pros = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->findAll();
		arsort($pros);
		$pros = array_slice($pros, 0, 10);

		$markers = array();
		foreach ($pros as $id => $pro) {
			$markers[$id] = new Marker();
			$markers[$id]->setPrefixJavascriptVariable('marker_');
			$markers[$id]->setPosition($pro->getAddress()->getLatitude(), $pro->getAddress()->getLongitude(), true);
			$markers[$id]->setAnimation(Animation::DROP);
			$markers[$id]->setOptions(array(
			    'clickable' => false,
			    'flat'      => true,
			));
			$markers[$id]->setIcon('/img/icones/map-marker-'.($id+1).'.png');
		}

	    $map = new Map();
		$map->setPrefixJavascriptVariable('map_');
		$map->setHtmlContainerId('map_canvas');
		$map->setAsync(true);
		$map->setAutoZoom(true);
		$map->setCenter($user->getAddress()->getLatitude(), $user->getAddress()->getLongitude(), true);
		$map->setMapOption('zoom', 3);
		$map->addMarker($marker);
		foreach ($markers as $m)
			$map->addMarker($m);
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
		    'width'  => '100%',
		    'height' => '400px',
		));
		$map->setLanguage('fr');

		$choices = array();
		$activities = $this->getDoctrine()->getRepository('MommyProfilBundle:JobActivities')->findAll();
		foreach ($activities as $activity) {
			$cat = $this->getDoctrine()->getRepository('MommyProfilBundle:JobActivitiesCategories')->find($activity->getCategory());
			$choices['category'][] = $cat->getDescFR();
			$choices['activity'][$cat->getDescFR()][$activity->getName()] = $activity->getDescFR();
		}

		return array(
			'map' => $map,
			'literal' => $user->getAddress()->getLiteral(),
			'pros' => $pros,
			'activities' => $choices['activity'],
			'categories' => $choices['category'],
			);
    }

	/**
	 * @Route("/stations.json", name="map-station.json")
	 * @Template
	 */
	public function stationsAction() {
		MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $method = $request->getMethod();
	    switch ($method) {
			case 'GET':
	    	default:
//		        $mem = new Memcached($this->container->getParameter('cache_domain'));
//		        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
                        $mem = $this->get('session');
                    
		        $return = $mem->get('map-list-stations');
		        if (!$return) {
		    		$stations = $this->getDoctrine()->getRepository('MommyMapBundle:Station')->findAll();
		    		$return = array();
		    		foreach ($stations as $station) 
		    			$return[] = array( 'id' => $station->getId(), 'name' => $station->getName().' ('.$station->getType().')');
		            $mem->set('map-list-stations', $return, 0);
		        }
		        $mem = null; unset($mem);

	    		return new JsonResponse($return);
	    }
	}

    /**
     * @Route("/glance/{addr}", requirements={"addr" = ".+"})
     * @Template
     */
    public function glanceAction($addr) {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $address = $request->query->get('address');

	    if (($address = $this->getDoctrine()->getRepository('MommyProfilBundle:Address')->find($addr)) === null)
	    	return new Response('Adresse inexistante', HTTP_BAD_REQUEST);

        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
        $geocoder = new \Geocoder\Geocoder();
        $chain    = new \Geocoder\Provider\ChainProvider(array(
            new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
            new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
            ));
        $geocoder->registerProvider($chain);

		$results = $geocoder->geocode($literal)->getResults();

		$marker = new Marker();
		// Configure your marker options
		$marker->setPrefixJavascriptVariable('marker_');
		$marker->setPosition($results[0]->getGeometry()->getLocation());
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
		$map->setCenter($results[0]->getGeometry()->getLocation());
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
			'literal' => $address->getLiteral()
			);
    }

}