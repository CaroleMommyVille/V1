<?php

namespace Mommy\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// User
use Mommy\SecurityBundle\Entity\User;

use Mommy\ClubBundle\Entity\Club;
use Mommy\PageBundle\Entity\Page;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/", name="search-index")
   * @Template
   */
	public function indexAction() {
		MommyUIBundle::logStatistics($this->get('request'));
		$request = $this->get('request');
		$session = $request->getSession();

		$form = $this->createFormBuilder()
            ->add('term', 'text', array(
            	'attr' => array(
            		'placeholder' => ''
            		),
            	)
            )
            ->getForm()
            ->createView();

		return array('form' => $form);
	}

	/**
	 * @Route("/term.json", name="search-term-json")
	 */
	public function termAction() {
		MommyUIBundle::logStatistics($this->get('request'));
		$request = $this->get('request');
		$session = $request->getSession();
		$results = array();
		$term = $request->query->get('term');
		if (filter_var($term, FILTER_VALIDATE_EMAIL)) {
			foreach ($this->getDoctrine()->getRepository('MommySecurityBundle:User')->findByEmail($term) as $user) {
				if ($user->getId() == $session->get('_uid')) continue;
		        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
		        $geocoder = new \Geocoder\Geocoder();
		        $chain    = new \Geocoder\Provider\ChainProvider(array(
		            new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//		            new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//		            new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
		            new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
		            ));
		        $geocoder->registerProvider($chain);

		        $detail = $user->getAge().' ans';
		        if (is_object($user->getAddress())) {
		        	$geocode = $geocoder->geocode($user->getAddress()->getLiteral());
		        	$detail .= ' - '.$geocode->getCity().', '.$getCountry();
		        }
				$results[] = array(
					'label' => $user->getFirstname().' '.substr($user->getLastname(), 0, 1),
					'detail' => $detail,
					'link' => '#/profil/voir/'.$user->getId(),
					'category' => 'Utilisateurs',
				);
			}
		}
		$repo = $this->getDoctrine()->getRepository('MommyClubBundle:Club');
		$clubs = $repo->createQueryBuilder('l')
            ->where("LOWER(l.keywords) like :keywords OR LOWER(l.desc_fr) like :desc OR LOWER(l.name) like :name")
            ->setParameters(array(
            	'keywords' => "%".strtolower($term)."%",
            	'desc' => "%".strtolower($term)."%",
            	'name' => "%".strtolower($term)."%"
            	)
            )
            ->getQuery()
            ->getResult();
		foreach ($clubs as $club) {
	        $detail = substr($club->getDescFR(), 0, 30).((strlen($club->getDescFR()) > 30) ? '...' : '' );
			$results[] = array(
				'label' => $club->getName(),
				'detail' => iconv(mb_detect_encoding($detail, mb_detect_order(), true), "UTF-8", $detail),
				'category' => 'MommyClub',
				'link' => '#/club/voir/'.$club->getId(),
			);
		}
		/*
		$repo = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro');
		$pages = $repo->createQueryBuilder('l')
            ->where("LOWER(l.description) like :desc OR LOWER(l.name) like :name")
            ->setParameters(array(
            	'desc' => "%".strtolower($term)."%",
            	'name' => "%".strtolower($term)."%"
            	)
            )
            ->getQuery()
            ->getResult();
		foreach ($pages as $page) {
			$results[] = array(
				'label' => $page->getName(),
				'detail' => substr($page->getDescFR(), 0, 30).((strlen($page->getDescFR()) > 30) ? '...' : '' ),
				'link' => '#/page/voir/'.$page->getId(),
				'category' => 'MommyPage',
			);
		}
		*/
		return new JsonResponse($results);
	}
}