<?php

namespace Mommy\ClubBundle\Controller;

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

use Mommy\ClubBundle\Form\ClubForm;
use Mommy\ClubBundle\Form\MessageForm;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ClubBundle\Entity\Club;
use Mommy\ClubBundle\Entity\Member;

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

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/display.json", name="club-display-json")
   */
  public function indexDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => '/club',
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
      "5" => array(
        'frame' => '#left',
        'name' => 'create-mc',
        'html' => '/club/creer/nouveau',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/reseau/tout/display.json", name="club-my-net-display-json")
   */
  public function myNetDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'network',
        'html' => '/club/reseau/tout',
        'title' => 'MommyReseau',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }


  /**
   * @Route("/creer/display.json", name="club-create-display-json")
   */
  public function createDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => '/club/creer',
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
      "5" => array(
        'frame' => '#left',
        'name' => 'create-mc',
        'html' => '/club/creer/nouveau',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/editer/{cid}/display.json", name="club-edit-display-json", requirements={"cid"=".+"})
   */
  public function editDisplayAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => "/club/editer/$cid",
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/dates/display.json", name="club-my-dates-display-json")
   */
  public function myDatesDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => '/club/dates',
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
      "5" => array(
        'frame' => '#left',
        'name' => 'create-mc',
        'html' => '/club/creer/nouveau',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/mes-clubs/display.json", name="club-my-display-json")
   */
  public function myClubsDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => '/club/mes-clubs',
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
      "5" => array(
        'frame' => '#left',
        'name' => 'create-mc',
        'html' => '/club/creer/nouveau',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/les-clubs/display.json", name="club-all-display-json")
   */
  public function allClubsDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => '/club/les-clubs',
        'title' => 'MommyClub',
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
        'name' => 'net-mc',
        'html' => '/club/reseau',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'my-mc',
        'html' => '/club/mes-clubs/sommaire',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'my-dates',
        'html' => '/club/dates/sommaire',
        'empty' => false,
      ),
*/
      "5" => array(
        'frame' => '#left',
        'name' => 'create-mc',
        'html' => '/club/creer/nouveau',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/voir/{cid}/display.json", name="club-voir-display-json", requirements={"cid"=".+"})
   */
  public function voirClubDisplayAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'club',
        'html' => "/club/voir/$cid",
        'title' => 'MommyClub',
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
        'name' => 'mc-bureau',
        'html' => "/club/bureau/$cid",
        'empty' => false,
      ),
/*
      "3" => array(
        'frame' => '#left',
        'name' => 'mc-dates',
        'html' => "/club/dates/sommaire/$cid",
        'empty' => false,
      ),
*/
      "4" => array(
        'frame' => '#left',
        'name' => 'map',
        'html' => "/club/localisation/$cid",
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/localisation/{cid}", name="club-localisation", requirements={"cid" = ".+"})
   * @Template
   */
  public function localisationAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid);
    if (is_null($club->getAddress()))
      return array(
        'map' => null,
        'literal' => '',
      );

    $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
    $geocoder = new \Geocoder\Geocoder();
    $chain    = new \Geocoder\Provider\ChainProvider(array(
      new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//      new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//      new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
      new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
    ));
    $geocoder->registerProvider($chain);

    $marker = new Marker();
    // Configure your marker options
    $marker->setPrefixJavascriptVariable('marker_');
    $marker->setPosition($club->getAddress()->getLatitude(), $club->getAddress()->getLongitude(), true);
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
    $map->setCenter($club->getAddress()->getLatitude(), $club->getAddress()->getLongitude(), true);
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
      'literal' => $club->getAddress()->getLiteral()
      );
  }

  /**
   * @Template
   */
  public function recurrentAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (!is_object($user))
      return array();

    $members = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findBy(array('isActive' => true, 'isLocked' => false), array('since' => 'DESC'));
    arsort($members);
    if (($key = array_search($user, $members)) !== false)
      unset($members[$key]);
    $members = array_slice($members, 0, 10);

    $messages = array();
    $likes = array();
    $comments = array();
    $previews = array();

    $msg = array();
    $cs = array();
    $clubs = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findByMember($user);
    foreach ($clubs as $club) {
      $msg = array_merge($msg, $this->getDoctrine()->getRepository('MommyClubBundle:Message')
        ->createQueryBuilder('l')
        ->where("l.wall = :club")
        ->orderBy('l.date', 'DESC')
        ->setParameters(array(
          'club' => $club->getClub()->getId(),
          )
        )
        ->setFirstResult(0)
        ->setMaxResults(10)
        ->getQuery()
        ->getResult());
      $cs[$club->getClub()->getId()] = $club->getClub();
    }
    foreach ($msg as $m) {
      $messages[$m->getDate(true)] = $m;
      $comments[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:Comment')->findBy(array('message' => $m), array('date' => 'ASC'));
      $likes[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $m->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($m->getLink())
        $previews[$m->getDate(true)] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($m->getLink());
    }

    return array(
      'messages' => $messages, 
      'likes' => $likes, 
      'comments' => $comments,
      'members' => $members,
      'clubs' => $cs,
      );
  }

  /**
   * @Route("/", name="club-index")
   * @Template
   */
  public function indexAction() {
  	MommyUIBundle::logStatistics($this->get('request'));
  	$request = $this->get('request');
    $session = $request->getSession();

  	$user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (!is_object($user))
      return array();

  	$club = array();
  	if ($user->getSince(true) < strtotime("-30 days")) {
      return $this->forward('MommyClubBundle:Default:recurrent', array());
    }

		$club['registration'] = array();

    if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) !== null) {
      if (!is_null($woman->getMarriage())) {
        switch ($woman->getMarriage()->getName()) {
          case 'Marriage-divorced':
          case 'Marriage-broken':
            $club['registration']['Les Moms séparées'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Moms séparées');
            break;
          case 'Marriage-alone':
            $club['registration']['Maman a fait un bébé toute seule'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman a fait un bébé toute seule');
            break;
          case 'Marriage-widow':
            $club['registration']['Papa avec les anges'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Papa avec les anges');
            break;
          case 'Marriage-tbd':
            $club['registration']['Tout reste à faire'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Tout reste à faire');
            break;
        }
      }
        
      if ($woman->getJobTitle()) {
        $club['registration']['Maman Super Mega Active'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman Super Mega Active');
      } elseif (is_object($woman->getNoWork())) {
        switch ($woman->getNoWork()->getName()) {
          case 'nowork-foyer':
            $club['registration']['En CDI de Maman'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En CDI de Maman');
            break;
          case 'nowork-conges':
            $club['registration']['En mode Maman et j’adooooore ça'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En mode Maman et j’adooooore ça');
            $club['registration']['En mode Maman et je deviens folle'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En mode Maman et je deviens folle');
            break;
          case 'nowork-anpe':
            $club['registration']['Maman cherche CDI, CDD, lecteur CD'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman cherche CDI, CDD, lecteur CD');
            break;
        }
      }

      if (is_object($woman->getStyle())) {
        switch ($woman->getStyle()->getName()) {
          case 'active':
            $club['registration']['Maman Super Mega Active'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman Super Mega Active');
            $club['registration']['En mode Maman et je deviens folle'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En mode Maman et je deviens folle');
            break;
          case 'home':
            $club['registration']['En CDI de Maman'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En CDI de Maman');
            $club['registration']['En mode Maman et j’adooooore ça'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En mode Maman et j’adooooore ça');
            $club['registration']['En mode Maman et je deviens folle'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('En mode Maman et je deviens folle');
            break;
          case 'fashion':
            $club['registration']['Les Bébés Haute Couture'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Bébés Haute Couture');
            $club['registration']['Mam’en Vogue'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Mam’en Vogue');
            break;
          case 'nature':
            $club['registration']['Koala, Kangourous, et autres animaux d’Australie'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Koala, Kangourous, et autres animaux d’Australie');
            $club['registration']['Bio et alternative'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Bio et alternative');
            $club['registration']['Koala, Kangourous, et autres animaux d’Australie'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Koala, Kangourous, et autres animaux d’Australie');
            break;
          case 'cocooning':
            $club['registration']['Koala, Kangourous, et autres animaux d’Australie'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Koala, Kangourous, et autres animaux d’Australie');
            $club['registration']['Co-Dodo Club'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Co-Dodo Club');
            $club['registration']['Milky Club'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Milky Club');
            break;
          case 'sportive':
            $club['registration']['Sport & Grossesse'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Sport & Grossesse');
            $club['registration']['Les Mamans Fit'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Fit');
            $club['registration']['Adieu mes kilos de grossesse'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Adieu mes kilos de grossesse');
            break;
          case 'traditional':
            $club['registration']['Les Mamans Catholiques de Paris'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Catholiques de Paris');
            $club['registration']['Les Mamans Protestantes de Paris'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Protestantes de Paris');
            $club['registration']['Les Mamans Musulmanes de Paris'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Musulmanes de Paris');
            $club['registration']['Les Mamans Juives de Paris'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Juives de Paris -');
            $club['registration']['Les Mamans Mixtes'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Mixtes');
            $club['registration']['Les Mamans Mixtes... et c’est pas facile tous les jours'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Les Mamans Mixtes... et c’est pas facile tous les jours');
            $club['registration']['Dura Lex Sed Lex'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Dura Lex Sed Lex');
            break;
          case 'zen':
            $club['registration']['YogiMom'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('YogiMom');
            break;
        }
      }

      if (is_array($woman->getHolidays())) {
        foreach ($woman->getHolidays() as $holidays) {
          switch ($holidays) {
            case 'holiday-itinerant':
            case 'holiday-camping':
              $club['registration']['Baroudeurs de l’aventuuuuuuuura'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Baroudeurs de l’aventuuuuuuuura');
              break;
            case 'holiday-mer':
              $club['registration']["Maman cultive l'effort"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Maman cultive l'effort");
              $club['registration']['Mamans Ile de Ré'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Mamans Ile de Ré');
              break;
            case 'holiday-france':
            case 'holiday-location':
            case 'holiday-maison':
              $club['registration']['Mamans Ile de Ré'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Mamans Ile de Ré');
              break;
            case 'holiday-croisiere':
              $club['registration']['Cruise Control'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Cruise Control');
              break;
            case 'holiday-etranger':
              $club['registration']['Maman autour du monde'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman autour du monde');
              break;
            case 'holiday-decouverte':
              $club['registration']['Baroudeurs de l’aventuuuuuuuura'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Baroudeurs de l’aventuuuuuuuura');
              $club['registration']['Maman autour du monde'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman autour du monde');
              break;
            case 'holiday-club':
            case 'holiday-farniente':
              $club['registration']['Maman, le soufflé se dégonfle'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Maman, le soufflé se dégonfle');
              break;
            case 'holiday-ville':
            case 'holiday-culture':
              $club['registration']["L'entrée des artistes"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("L'entrée des artistes");
              break;
            case 'holiday-escapade':
              $club['registration']["Chérie Chéri"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Chérie Chéri");
              break;
            case 'holiday-montagne':
            case 'holiday-campagne':
              $club['registration']["Baroudeurs de l’aventuuuuuuuura"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Baroudeurs de l’aventuuuuuuuura");
              $club['registration']["Maman cultive l'effort"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Maman cultive l'effort");
              break;
            case 'holiday-sport':
              $club['registration']["Maman cultive l'effort"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Maman cultive l'effort");
              break;
          }
        }
      }
    }

    if (($pregnancy = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) !== null) {
      if (is_object($pregnancy->getSpeed())) {
        switch ($pregnancy->getSpeed()->getName()) {
          case 'speed-nonprevu':
            $club['registration']["Je suis Enceinte ???!!!"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Je suis Enceinte ???!!!");
            break;
          case 'speed-pma':
            $club['registration']["Mon bébé à tout prix !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Mon bébé à tout prix !");
            break;
          case 'speed-trop':
            $club['registration']["Je n'y croyais plus !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Je n'y croyais plus !");
            break;
          case 'speed-temps':
            $club['registration']["Ca y est ! Enfin !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Ca y est ! Enfin !");
            break;
          case 'speed-rapidement':
            $club['registration']["Déjà enceinte ?!"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Déjà enceinte ?!");
            break;
        }
      }
    }

    if (($project = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) !== null) {
      if (is_object($project->getBreastfed())) {
        switch ($project->getBreastfed()->getName()) {
          case 'breastfed-sein':
            $club['registration']["Oui, non, peut-être !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Oui, non, peut-être !");
            $club['registration']["Aie aie aie ! Au secours !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Aie aie aie ! Au secours !");
            $club['registration']["La bien heureuse !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("La bien heureuse !");
            break;
          case 'breastfed-biberon':
            $club['registration']["Le Bibi de mon bébé"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Le Bibi de mon bébé");
            $club['registration']["Sein tu ne veux pas, biberon tu auras !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Sein tu ne veux pas, biberon tu auras !");
            break;
        }
      }
      if (!is_null($project->getPreparation())) {
        $club['registration']["Accoucher : préparation ou pas ?"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Accoucher : préparation ou pas ?");
      }
    }

    if (($mother = $this->getDoctrine()->getRepository('MommyProfilBundle:Mother')->findOneByUser($user)) !== null) {
      if (!is_null($mother->getBabyBlues()) && $mother->getBabyBlues())
        $club['registration']["Baby Blues quand tu nous tiens !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Baby Blues quand tu nous tiens !");
      if (!is_null($mother-getWeightOk()) && $mother->getWeightOk())
        $club['registration']["I want my body back"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("I want my body back");
      if (is_object($mother->getAfter())) {
        switch ($mother->getAfter()->getName()) {
          case 'after-conge':
            $club['registration']["Congé parental, vous avez dit congé ?"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Congé parental, vous avez dit congé ?");
            break;
          case 'after-travail':
            $club['registration']["Retour au taf ? Le Tapis Rouge ou Placard ?"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Retour au taf ? Le Tapis Rouge ou Placard ?");
            break;
        }
      }
      if (!is_null($mother->getBetween()) && ($mother->getBetween() > 0) && ($mother->getBetween() <= 6))
        $club['registration']["Allez, on y retourne (et vite) !"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Allez, on y retourne (et vite) !");
    }

    $children = $this->getDoctrine()->getRepository('MommyProfilBundle:Child')->findByUser($user);
    foreach ($children as $child) {
      if (is_object($child->getPregnancyStatus())) {
        switch ($child->getPregnancyStatus()->getName()) {
          case 'status-oui':
            $club['registration']["A peine eu le temps de me rendre compte que j'étais enceinte..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("A peine eu le temps de me rendre compte que j'étais enceinte...");
            break;
          case 'status-presque':
            $club['registration']["Jusqu'ici tout va bien jusqu'au jour où..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Jusqu'ici tout va bien jusqu'au jour où...");
            break;
          default:
            $club['registration']["Ma grossesse ma galère"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Ma grossesse ma galère");
            break;
        }
      }
      if (is_object($child->getPreparation())) {
        $club['registration']["Accoucher : préparation ou pas ?"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Accoucher : préparation ou pas ?");
      }
    }

    if (($trim1 = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnancy)) !== null) {
      if (is_object($trim1->getStatus())) {
        switch ($trim1->getStatus()->getName()) {
          case 'status-oui':
            $club['registration']["A peine eu le temps de me rendre compte que j'étais enceinte..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("A peine eu le temps de me rendre compte que j'étais enceinte...");
            break;
          case 'status-presque':
            $club['registration']["Jusqu'ici tout va bien jusqu'au jour où..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Jusqu'ici tout va bien jusqu'au jour où...");
            break;
          default:
            $club['registration']["Ma grossesse ma galère"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Ma grossesse ma galère");
            break;
        }
      }
    }
    if (($trim2 = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnancy)) !== null) {
      if (is_object($trim2->getStatus())) {
        switch ($trim2->getStatus()->getName()) {
          case 'status-oui':
            $club['registration']["A peine eu le temps de me rendre compte que j'étais enceinte..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("A peine eu le temps de me rendre compte que j'étais enceinte...");
            break;
          case 'status-presque':
            $club['registration']["Jusqu'ici tout va bien jusqu'au jour où..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Jusqu'ici tout va bien jusqu'au jour où...");
            break;
          default:
            $club['registration']["Ma grossesse ma galère"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Ma grossesse ma galère");
            break;
        }
      }
    }
    if (($trim3 = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnancy)) !== null) {
      if (is_object($trim3->getStatus())) {
        switch ($trim3->getStatus()->getName()) {
          case 'status-oui':
            $club['registration']["A peine eu le temps de me rendre compte que j'étais enceinte..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("A peine eu le temps de me rendre compte que j'étais enceinte...");
            break;
          case 'status-presque':
            $club['registration']["Jusqu'ici tout va bien jusqu'au jour où..."] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Jusqu'ici tout va bien jusqu'au jour où...");
            break;
          default:
            $club['registration']["Ma grossesse ma galère"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("Ma grossesse ma galère");
            break;
        }
      }
    }

    $almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($user);
  	if (is_object($almost) && ($almost->getSince(true) > strtotime("-6 months"))) {
			$club['registration']['Un an et un jour plus tard...'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Un an et un jour plus tard...');
      if (!is_null($almost->getSo())) {
    		if ($almost->getSo()->getName() != 'pregnancy-so-1') {
  				$club['registration']['Tomber enceinte ? Je préfèrerais atterrir tranquillement moi'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Tomber enceinte ? Je préfèrerais atterrir tranquillement moi');
  			}
      }
      if (!is_null($almost->getDad())) {
  			if (($almost->getDad()->getName() == 'pregnancy-dad-4')
  			 	|| ($almost->getDad()->getName() == 'pregnancy-dad-5')) {
    			$club['registration']["C'est un Papa qui dit oui, qui dit non"] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName("C'est un Papa qui dit oui, qui dit non");
    		}
      }
      if (!is_null($almost->getMood())) {
  			if (($almost->getMood()->getName() == 'pregnancy-mood-4')
  			 	|| ($almost->getMood()->getName() == 'pregnancy-mood-6')) {
    			$club['registration']['Tomber enceinte ? Je préfèrerais atterrir tranquillement moi'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Tomber enceinte ? Je préfèrerais atterrir tranquillement moi');
    		}
      }
      if (!is_null($almost->getHope())) {
  			if ($almost->getHope()->getName() != 'pregnancy-hope-fille') {
  				$club['registration']['Par ici ma fille !'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Par ici ma fille !');
    		} else if ($almost->getHope()->getName() != 'pregnancy-hope-garcon') {
    			$club['registration']['Mon fils, mon fils !'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Mon fils, mon fils !');
    		}
      }
      $club['registration']['Adieu, pilule !'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('Adieu, pilule !');
  	}

    if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) !== null) {
      $club['registration']['7 lettres, 3 mots, pour nos petits anges'] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findOneByName('7 lettres, 3 mots, pour nos petits anges');
    }

    $club['friends'] = array();
    foreach($this->getDoctrine()->getRepository('MommyClubBundle:ClubRecommanded')->findByDest($user) as $reco) {
      $club['friends'][] = $reco;
    }

    $members = array();
    $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user);
    switch ($type->getType()->getName()) {
      case 'type-presquenceinte':
        $members = $this->getPresquEnceinte($user);
        break;
      case 'type-enceinte':
        $members = $this->getEnceinte($user);
        break;
      case 'type-maman':
        $members = $this->getMaman($user);
        break;
      case 'type-pma':
        $members = $this->getPma($user);
        break;
      case 'type-mamange':
        $members = $this->getMamange($user);
        break;
      case 'type-adoptante':
        $members = $this->getAdoptante($user);
        break;
    }
    if (!sizeof($members)) {
      $members = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findBy(array('isActive' => true, 'isLocked' => false), array('since' => 'DESC'));
      arsort($members);
      if (($key = array_search($user, $members)) !== false)
        unset($members[$key]);
      $members = array_slice($members, 0, 10);
    }

    return array(
      'club' => $club,
      'members' => $members,
      );
  }

  private function sameWoman(User $user, User $mom) {
    $self =  $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user);
    $womanU = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user);
    $womanM = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($mom);
    $nolang = true;
    if (is_array($womanU->getLanguages()) && sizeof($womanU->getLanguages())) {
      if (!is_array($womanM->getLanguages()) || !sizeof($womanM->getLanguages())) return false;
      foreach ($womanU->getLanguages() as $language) {
        if ($language == 'language-french') continue;
        if (in_array($language, $womanM->getLanguages()))
          $nolang = false;
      }
      if (!$nolang) return false;
    } else
      return false;
    if (($mom->getAge() < $user->getAge()-5) || ($mom->getAge() > $user->getAge()+5)) return false;
    if (!is_object($self->getMarriage())) return false;
    if (!is_object($mom->getMarriage())) return false;
    if (($self->getMarriage()->getName() == 'Marriage-widow') && ($self->getMarriage() != $mom->getMarriage())) return false;
    $nostation = true;
    if (is_array($self->getStations()) && sizeof($self->getStations())) {
      if (!is_array($mom->getStations()) || !sizeof($mom->getStations())) return false;
      foreach ($self->getStations() as $station) {
        if (in_array($station, $woman->getStations()))
          $nostation = false;
      }
      if ($nostation) return false;
    } else
      return false;
    // TBD - Distance
    if (!is_object($self->getDiploma())) return false;
    if (!is_object($mom->getDiploma())) return false;
    if ($self->getDiploma() != $mom->getDiploma()) return false;
    $nostyle = true;
    if (is_array($self->getStyle()) && sizeof($self->getStyle())) {
      if (!is_array($mom->getStyle()) || !sizeof($mom->getStyle())) return false;
      foreach ($self->getStyle() as $style) {
        if (in_array($style, $woman->getStyle()))
          $nostyle = false;
      }
      if ($nostyle) return false;
    } else
      return false;
    if (in_array($self->getMarriage()->getName(), array('Marriage-alone', 'Marriage-tbd')) && !in_array($mom->getMarriage()->getName(), array('Marriage-alone', 'Marriage-tbd')))
      return false;
    else if ($self->getMarriage() != $mom->getMarriage())
      return false;
    $nosport = true;
    if (is_array($self->getSports()) && sizeof($self->getSports())) {
      if (!is_array($mom->getSports()) || !sizeof($mom->getSports())) return false;
      foreach ($self->getSports() as $sport) {
        if (in_array($sport, $woman->getSports()))
          $nosport = false;
      }
      if ($nosport) return false;
    } else
      return false;
    $noactivity = true;
    if (is_array($self->getActivities()) && sizeof($self->getActivities())) {
      if (!is_array($mom->getActivities()) || !sizeof($mom->getActivities())) return false;
      if ((sizeof($self->getActivities()) == 1) || (sizeof($mom->getActivities()) == 1))
        $noactivity = false;
      foreach ($self->getActivities() as $activity) {
        if (in_array($activity, $woman->getActivities()))
          $noactivity = false;
      }
      if ($noactivity) return false;
    } else
      return false;
    return true;
  }

  private function getPresquEnceinte(User $user) {
    $members = array();
    if (($almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($user)) === null)
      return $members;
    if (!is_null($almost->getSo())) {
      $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findBy(array('so' => $almost->getSo()), array());
      foreach ($moms as $mom) {
        if (!is_object($almost->getDad())) break;
        if (!is_object($mom->getDad())) continue;
        if ($mom->getDad() != $almost->getDad()) continue;
        if (!is_object($almost->getMood())) break;
        if (!is_object($mom->getMood())) continue;
        if ($mom->getMood() != $almost->getMood()) continue;
        if ($this->sameWoman($user, $mom->getUser()))
          $members[] = $mom->getUser();
      }
    }
    return $members;
  }

  private function getEnceinte(User $user) {
    $members = array();
    if (($pregnant = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) === null)
      return $members;
    if ($pregnant->getPrems()) {
      $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findBy(array('prems' => $pregnant->getPrems()), array());
    } else {
      $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findAll();
    }
    foreach ($moms as $mom) {
      if (($mom->getAmenorrhee() < $pregnant->getAmenorrhee()-1) || ($mom->getAmenorrhee() > $pregnant->getAmenorrhee()+1)) return false;
      if (($trimU = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnant)) === null) {
        if (($trimU = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnant)) === null) {
          if (($trimU = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnant)) === null) {
            continue;
          }
        }
      }
      $pregnantM = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($mom->getUser());
      if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnantM)) === null) {
        if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnantM)) === null) {
          if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnantM)) === null) {
            continue;
          }
        }
      }
      $nopathobb = true;
      if (is_array($trimU->getPathologyPregnancy()) && sizeof($trimU->getPathologyPregnancy())) {
        if (!is_array($trimM->getPathologyPregnancy()) || !sizeof($trimM->getPathologyPregnancy())) return false;
        foreach ($trimU->getPathologyPregnancy() as $patho) {
          if (in_array($patho, $trimM->getPathologyPregnancy()))
            $nopathobb = false;
        }
        if ($nopathobb) continue;
      } else
        continue;
      $nopathogro = true;
      if (is_array($trimU->getPathologyBaby()) && sizeof($trimU->getPathologyBaby())) {
        if (!is_array($trimM->getPathologyBaby()) || !sizeof($trimM->getPathologyBaby())) return false;
        foreach ($trimU->getPathologyBaby() as $patho) {
          if (in_array($patho, $trimM->getPathologyBaby()))
            $nopathogro = false;
        }
        if ($nopathogro) continue;
      } else
        continue;

      if (($planU = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null) continue;
      if (($planM = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($mom->getUser())) === null) continue;
      if (!$planU->getMaternityFound() && ($planU->getMaternityFound() != $planM->getMaternityFound())) continue;
      if ($planU->getMaternityFound()) {
        if ($planU->getMaternity() != $planM->getMaternity()) continue;
      }
      $noprep = true;
      if (is_array($planU->getPreparation()) && sizeof($planU->getPreparation())) {
        if (!is_array($planM->getPreparation()) || !sizeof($planM->getPreparation())) return false;
        foreach ($planU->getPreparation() as $prep) {
          if (in_array($prep, $planM->getPreparation()))
            $noprep = false;
        }
        if ($noprep) continue;
      } else
        continue;
      if ($planU->getBreastfed() != $planM->getBreastfed()) continue;
      if (in_array($planU->getFood()->getName(), array('alimentation-bio', 'alimentation-sans-gluten')) && !in_array($planM->getFood()->getName(), array('alimentation-bio', 'alimentation-sans-gluten'))) continue;
      $nosymptom = true;
      if (is_array($trimU->setPregnancySymptoms()) && sizeof($trimU->setPregnancySymptoms())) {
        if (!is_array($trimM->setPregnancySymptoms()) || !sizeof($trimM->setPregnancySymptoms())) return false;
        foreach ($trimU->setPregnancySymptoms() as $symptom) {
          if (in_array($symptom, $trimM->setPregnancySymptoms()))
            $nosymptom = false;
        }
        if ($nosymptom) continue;
      } else
        continue;
      if ($this->sameWoman($user, $mom->getUser()))
        $members[] = $mom->getUser();
    }
    return $members;
  }

  private function getMaman(User $user) {
    $members = array();
    if (($familyU = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user)) === null)
      return $members;
    $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findBy(array('size' => $familyU->getSize()), array());
    foreach ($moms as $mom) {
      if (($mom->getUser()->getAge() < $user->getAge()-1) || ($mom->getUser()->getAge() > $user->getAge()+1)) return false;
      // pathobb
      // patho maman ??
      if ($this->sameWoman($user, $mom->getUser()))
        $members[] = $mom->getUser();
    }
    return $members;
  }

  private function getPma(User $user) {
    $members = array();
    if (($pmaU = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
      return $members;
    $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findBy(array('pregnant' => $pmaU->getPregnant()), array());
    foreach ($moms as $mom) {
      if (($mom->getAmenorrhee() < $pmaU->getAmenorrhee()-1) || ($mom->getAmenorrhee() > $pmaU->getAmenorrhee()+1)) return false;
      $nopatho = true;
      if (is_array($pmaU->getPathologyGyneco()) && sizeof($pmaU->getPathologyGyneco())) {
        if (!is_array($mom->getPathologyGyneco()) || !sizeof($mom->getPathologyGyneco())) return false;
        foreach ($pmaU->getPathologyGyneco() as $patho) {
          if (in_array($patho, $mom->getPathologyGyneco()))
            $nopatho = false;
        }
        if ($nopatho) continue;
      } else
        continue;
      $nostimul = true;
      if (is_array($pmaU->getOvulationStimulator()) && sizeof($pmaU->getOvulationStimulator())) {
        if (!is_array($mom->getOvulationStimulator()) || !sizeof($mom->getOvulationStimulator())) return false;
        foreach ($pmaU->getOvulationStimulator() as $stimul) {
          if (in_array($stimul, $mom->getOvulationStimulator()))
            $nostimul = false;
        }
        if ($nostimul) continue;
      } else
        continue;
      $notech = true;
      if (is_array($pmaU->getTechPMA()) && sizeof($pmaU->getTechPMA())) {
        if (!is_array($mom->getTechPMA()) || !sizeof($mom->getTechPMA())) return false;
        foreach ($pmaU->getTechPMA() as $tech) {
          if (in_array($tech, $mom->getTechPMA()))
            $notech = false;
        }
        if ($notech) continue;
      } else
        continue;
      $nosoft = true;
      if (is_array($pmaU->getSoftMethod()) && sizeof($pmaU->getSoftMethod())) {
        if (!is_array($mom->getSoftMethod()) || !sizeof($mom->getSoftMethod())) return false;
        foreach ($pmaU->getSoftMethod() as $soft) {
          if (in_array($soft, $mom->getSoftMethod()))
            $nosoft = false;
        }
        if ($nosoft) continue;
      } else
        continue;
      if ($this->sameWoman($user, $mom->getUser()))
        $members[] = $mom->getUser();
    }
    return $members;
  }

  private function getMamange(User $user) {
    $members = array();
    if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
      return $members;
    if (is_null($mamange->getCase())) return $members;
    $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findBy(array('case' => $mamange->getCase()), array());
    foreach ($moms as $mom) {
      if (is_null($mamange->getDisease())) continue;
      if (is_null($mom->getDisease())) continue;
      if ($mamange->getDisease() != $mom->getDisease()) continue;
      if (is_null($mamange->getCouple())) continue;
      if (is_null($mom->getCouple())) continue;
      if ($mamange->getCouple() != $mom->getCouple()) continue;
      if (is_null($mamange->getBaby())) continue;
      if (is_null($mom->getBaby())) continue;
      if ($mamange->getBaby() != $mom->getBaby()) continue;
      if ($this->sameWoman($user, $mom->getUser()))
        $members[] = $mom->getUser();
    }
    return $members;
  }

  private function getAdoptante(User $user) {
    $members = array();
    $moms = $this->getDoctrine()->getRepository('MommyProfilBundle:Adoption')->findAll();
    foreach ($moms as $mom) {
      if ($this->sameWoman($user, $mom->getUser()))
        $members[] = $mom->getUser();
    }
    return $members;
  }

  /**
   * @Route("/reseau", name="club-network")
   * @Template
   */
  public function networkAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyProfilBundle:Network');
    $network = $repo->createQueryBuilder('l')
      ->where("l.source = :user OR l.dest = :user")
      ->setParameters(array(
        'user' => $user->getId(),
        )
      )
      ->setMaxResults(4)
      ->getQuery()
      ->getResult();
    $moms = array();
    foreach ($network as $mom) {
      if ($mom->getSource() == $user)
        $moms[] = $mom->getDest();
      else
        $moms[] = $mom->getSource();
    }
    return array('network' => $moms);
  }

  /**
   * @Route("/reseau/tout", name="club-my-net")
   * @Template
   */
  public function myNetAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyProfilBundle:Network');
    $network = $repo->createQueryBuilder('l')
      ->where("l.source = :user OR l.dest = :user")
      ->setParameters(array(
        'user' => $user->getId(),
        )
      )
      ->getQuery()
      ->getResult();
    $moms = array();
    foreach ($network as $mom) {
      if ($mom->getSource() == $user)
        $moms[] = $mom->getDest();
      else
        $moms[] = $mom->getSource();
    }
    return array('network' => $moms);
  }

  /**
   * @Route("/mes-clubs", name="club-my")
   * @Template
   */
  public function myClubAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findByMember($user);

    $msg = array();
    foreach ($repo as $club) {
      $msg[$club->getClub()->getId()] = count($this->getDoctrine()->getRepository('MommyClubBundle:Message')->findByWall($club->getClub()));
    }
    arsort($msg);
    $msg = array_slice($msg, 0, 3, true);
    $clubs = array();
    foreach ($msg as $key => $m) 
      $clubs[] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($key);
    return array(
      'clubs' => $clubs,
      );
  }

  /**
   * @Route("/les-clubs", name="club-all")
   * @Template
   */
  public function allClubAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->findAll();

    $msg = array();
    foreach ($repo as $club) {
      $msg[$club->getId()] = count($this->getDoctrine()->getRepository('MommyClubBundle:Message')->findByWall($club));
    }
    arsort($msg);
    $clubs = array();
    foreach ($msg as $key => $m) 
      $clubs[] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($key);
    return array(
      'clubs' => $clubs,
      );
  }

  /**
   * @Route("/rejoindre/{cid}", name="club-join", requirements={"cid" = ".+"})
   * @Template
   */
  public function joinClubAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid)) === null)
      return new Response("Ce club n'existe pas", Response::HTTP_BAD_REQUEST);
    if (($member = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findOneBy(array('club' => $club, 'member' => $user), array())) === null) {
      $em = $this->getDoctrine()->getManager();
      $member = new Member();
      $member->setClub($club);
      $member->setMember($user);
      $em->persist($member);
      $em->flush();
      $em->clear();
    }

    return $this->forward('MommyClubBundle:Default:voir', array('cid' => $cid));
  }

  /**
   * @Route("/mes-clubs/sommaire", name="club-my-summary")
   * @Template
   */
  public function myClubSummaryAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findByMember($user);

    $msg = array();
    foreach ($repo as $club) {
      $msg[$club->getClub()->getId()] = count($this->getDoctrine()->getRepository('MommyClubBundle:Message')->findByWall($club->getClub()));
    }
    arsort($msg);
    //$msg = array_slice($msg, 0, 4, true);
    $clubs = array();
    $count = 0;
    foreach ($msg as $key => $m) {
      $clubs[] = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($key);
      $count++;
      if ($count == 4) break;
    }
    return array(
      'clubs' => $clubs,
      );
  }

  /**
   * @Route("/date/{did}", name="club-date", requirements={"did"=".+"})
   * @Template
   */
  public function dateAction($did) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($did)) === null)
      return new Response("Le club demandé n'existe pas", Response::HTTP_BAD_REQUEST);

    $event = $this->getDoctrine()->getRepository('MommyClubBundle:Event')->find($did);

    if (($attendee = $this->getDoctrine()->getRepository('MommyClubBundle:Attendee')->findOneBy(array('event' => $event, 'member' => $user), array())) === null)
      return new Response("Vous n'avez pas accès à l'évènement demandé", Response::HTTP_FORBIDDEN);

    $attendees = $this->getDoctrine()->getRepository('MommyClubBundle:Attendee')->findByEvent($event);

    return array(
      'event' => $event,
      'attendees' => $attendees,
      );
  }

  /**
   * @Route("/voir/{cid}", name="club-voir", requirements={"cid"=".+"})
   * @Template
   */
  public function voirAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid)) === null)
      return new Response("Le club demandé n'existe pas", Response::HTTP_BAD_REQUEST);
    if (($member = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findOneBy(array('club' => $club, 'member' => $user), array())) === null) {
      //return new Response("Vous n'avez pas accès au club demandé", Response::HTTP_FORBIDDEN);
      $full = false;
    } else {
      $full = true;
    }

    if ($user == $club->getFounder())
      $edit = true;
    else if (in_array($user->getId(), $club->getAdmins()))
      $edit = true;
    else
      $edit = false;

    $members = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findBy(array('club' => $club), array());

    $messages = $this->getDoctrine()->getRepository('MommyClubBundle:Message')
      ->createQueryBuilder('l')
      ->where("l.wall = :club")
      ->orderBy('l.date', 'DESC')
      ->setParameters(array(
        'club' => $club->getId(),
        )
      )
      ->setFirstResult(0)
      ->setMaxResults(10)
      ->getQuery()
      ->getResult();

    $likes = array();
    $comments = array();
    $previews = array();
    foreach ($messages as $message) {
      $comments[$message->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:Comment')->findBy(array('message' => $message), array('date' => 'ASC'));
      $likes[$message->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $message->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($message->getLink() !== null) {
        $previews[$message->getId()] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($message->getLink());
      }
    }

    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));

    $like = $this->getDoctrine()->getRepository('MommyClubBundle:Member')
        ->createQueryBuilder('m')
        ->select('count(m.id)')
        ->where('m.club = :club')
        ->setParameters(array(
          'club' => $club->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();

    return array(
      'club' => $club,
      'likes' => $likes,
      'messages' => $messages,
      'comments' => $comments,
      'previews' => $previews,
      'action' => "/club/post/$cid",
      'form' => $form->createView(),
      'page_like' => "$cid",
      'page_like_nb' => $like,
      'full' => $full,
      'members' => $members,
      'edit' => $edit,
      );
  }

  /**
   * @Route("/dates", name="club-dates")
   * @Template
   */
  public function datesAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Event');
    $dates = $repo->createQueryBuilder('e')
      ->join('MommyClubBundle:Member', 'm')
      ->where('m.club = e.club and e.date > :time')
      ->orderBy('e.date', 'ASC')
      ->setMaxResults('5')
      ->setParameters(array(
        'time' => time()
        )
      )
      ->getQuery()
      ->getResult();

    return array(
      'dates' => $dates,
      );
  }

  /**
   * @Route("/dates/sommaire/{cid}", name="club-dates-summary", defaults={"cid"=null})
   * @Template
   */
  public function datesSummaryAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (($cid === null) || !is_int($cid)) {
      $long = false;
      $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
      $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Attendee');
      $dates = $repo->createQueryBuilder('a')
        ->leftJoin('MommyClubBundle:Event', 'e')
        ->where('a.member = :uid and a.event = e.id and e.date > :time')
        ->orderBy('e.date', 'ASC')
        ->setMaxResults('5')
        ->setParameters(array(
          'uid' => $user->getId(),
          'time' => time(),
          )
        )
        ->getQuery()
        ->getResult();
    } else {
      $long = true;
      $repo = $this->getDoctrine()->getRepository('MommyClubBundle:Event');
      $dates = $repo->createQueryBuilder('e')
        ->where('e.club = :cid')
        ->orderBy('e.date', 'ASC')
        ->setMaxResults('5')
        ->setParameters(array(
          'cid' => $cid,
          )
        )
        ->getQuery()
        ->getResult();
    }

    return array(
      'dates' => $dates,
      'long' => $long,
      );
  }

  /**
   * @Route("/creer", name="club-create")
   * @Template
   */
  public function createAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (!is_object($session->get('_user'))) {
      return new Response('Vous ne pouvez éditer ce club', Response::HTTP_FORBIDDEN);
    }
    $uid = $session->get('_user')->getId();
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    $form = $this->createForm(new ClubForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (! $form->isValid()) {
        return new Response('Réponses invalides', Response::HTTP_BAD_REQUEST);
      }
      $update = $form->getData();
      $club = new Club();
      $club->setFounder($user);
      $club->setName($update['name']);
      $club->setDescFR($update['description']);
      $club->setKeys($update['keywords']);
      if (!is_null($update['address']))
        $club->setAddress($update['address']);
      if (is_object($update['photo']) && $update['photo']->getClientSize()) {
        $url = sprintf(
          '%s%s',
          $this->container->getParameter('aws_base_url'),
          $this->getPhotoUploaderS3()->upload($update['photo'])
        );
        $club->setPhoto($url);
        $club->setEnabled(true);
      }
      $em = $this->getDoctrine()->getManager();
      $em->persist($club);
      $member = new Member();
      $member->setClub($club);
      $member->setMember($user);
      $em->persist($member);
      $em->flush();
      $em->clear();

      return $this->forward('MommyClubBundle:Default:voir', array('cid' => $club->getId()));
    }
    return array(
      'form' => $form->createView(),
    );
  }

  /**
   * @Route("/editer/{cid}", name="club-edit", requirements={"cid"=".+"})
   * @Template
   */
  public function editAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (!is_object($session->get('_user'))) {
      return new Response('Vous ne pouvez éditer ce club', Response::HTTP_FORBIDDEN);
    }
    $uid = $session->get('_user')->getId();
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);
    $club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid);
    if ($club->getFounder() != $user)
      return new Response('Vous ne pouvez éditer ce club', Response::HTTP_FORBIDDEN);

    $form = $this->createForm(new ClubForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (! $form->isValid()) {
        return new Response('Réponses invalides', Response::HTTP_BAD_REQUEST);
      }
      $update = $form->getData();
      $club->setName($update['name']);
      $club->setDescFR($update['description']);
      $club->setKeys($update['keywords']);
      if (!is_null($update['address']))
        $club->setAddress($update['address']);

      if (is_object($update['photo']) && $update['photo']->getClientSize()) {
        $url = sprintf(
          '%s%s',
          $this->container->getParameter('aws_base_url'),
          $this->getPhotoUploaderS3()->upload($update['photo'])
        );
        $club->setPhoto($url);
        $club->setEnabled(true);
      }
      $em = $this->getDoctrine()->getManager();
//      $em->persist($club);
      $em->flush();
      $em->clear();

      return $this->forward('MommyClubBundle:Default:voir', array('cid' => $club->getId()));
    }
    return array(
      'form' => $form->createView(),
      'club' => $club,
    );
  }

  /**
   * @Route("/creer/nouveau", name="club-create-new")
   * @Template
   */
  public function createNewAction() {
    return array();
  }

  protected function getPhotoUploaderS3() {
    return $this->get('amazonS3.photo_uploader');
  }

  protected function getPhotoUploaderHelios() {
    return $this->get('helios.photo_uploader');
  }

  /**
   * @Route("/bureau/{cid}", name="club-bureau", requirements={"cid"=".+"})
   * @Template
   */
  public function bureauAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $bureau = array();
    $club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid);
    $bureau[] = $club->getFounder();
    foreach ($club->getAdmins() as $admin) {
      $bureau[] = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($admin);
    }
    return array('bureau' => $bureau);
  }

  /**
   * @Route("/derniers", name="club-last-members")
   * @Template
   */
  public function lastAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $members = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findAll(); 
    arsort($members);
    $members = array_slice($members, 0, 10);

    $users = array();
    foreach ($members as $member)
      if (!in_array($member->getMember()->getId(), $users))
        $users[] = $member->getMember()->getId();

    return array(
      'members' => $users,
      );
  }
}