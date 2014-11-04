<?php

namespace Mommy\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;

// Container
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Mommy\SecurityBundle\Form\WomanForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;
use Mommy\StatsBundle\Controller\UsersController as MommyStatsBundle;

// TemporaryURL
use Mommy\SecurityBundle\Entity\TemporaryUrl;

class WomanController extends Controller implements ContainerAwareInterface
{
	private $step = 'femme';

	private $form = null;

	protected $container;

	public function __construct(ContainerInterface $container) {
        $this->setContainer($container);
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

	public function getStep() {
		return $this->step;
	}

    public function getOptions() {
        $choices = array();

        $choices['languages'] = array();
        $languages = $this->getDoctrine()->getRepository('MommyProfilBundle:Language')->findAll();
        foreach ($languages as $item) {
            $choices['languages'][$item->getName()] = $item->getDescFR();
        }

        $choices['spheres'] = array();
        $spheres = $this->getDoctrine()->getRepository('MommyProfilBundle:Sphere')->findAll();
        foreach ($spheres as $item) {
            if (!isset($choices['spheres'][$item->getLanguage()->getName()])) $choices['spheres'][$item->getLanguage()->getName()] = array();
            $choices['spheres'][$item->getLanguage()->getName()][$item->getName()] = $item->getDescFR();
        }

        $choices['activities'] = array();
        $activities = $this->getDoctrine()->getRepository('MommyProfilBundle:Activity')->findAll();
        foreach ($activities as $item) {
            $choices['activities'][$item->getName()] = $item->getDescFR();
        }
        sort($choices['activities']);

        $choices['sports'] = array();
        $sports = $this->getDoctrine()->getRepository('MommyProfilBundle:Sport')->findAll();
        foreach ($sports as $item) {
            $choices['sports'][$item->getName()] = $item->getDescFR();
        }
        sort($choices['sports']);

        $choices['holidays'] = array();
        $holidays = $this->getDoctrine()->getRepository('MommyProfilBundle:Holiday')->findAll();
        foreach ($holidays as $item) {
            $choices['holidays'][$item->getName()] = $item->getDescFR();
        }

        $choices['movies'] = array();
        $movies = $this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findAll();
        foreach ($movies as $item) {
            $choices['movies'][$item->getName()] = $item->getDescFR();
        }

        $choices['tvshows'] = array();
        $tvshows = $this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findAll();
        foreach ($tvshows as $item) {
            $choices['tvshows'][$item->getName()] = $item->getDescFR();
        }

        $choices['musics'] = array();
        $musics = $this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findAll();
        foreach ($musics as $item) {
            $choices['musics'][$item->getName()] = $item->getDescFR();
        }

        $choices['vip'] = array();
        $vip = $this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findAll();
        foreach ($vip as $item) {
            $choices['vip'][$item->getName()] = $item->getDescFR();
        }

        $choices['style'] = array();
        $style = $this->getDoctrine()->getRepository('MommyProfilBundle:Style')->findAll();
        foreach ($style as $item) {
            $choices['style'][$item->getName()] = $item->getDescFR();
        }
        return $choices;
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new WomanForm($this->getDoctrine()->getManager(), $this->container));
		return $this->form;
	}

	public function getFormView() {
		$this->getForm();
		return $this->form->createView();
	}

    public function handleForm() {
		$request = $this->get('request');
        $session = $request->getSession();

		$this->getForm();
		$this->form->handleRequest($request);
        if (! $this->form->isValid()) {
        	if ($this->get('kernel')->getEnvironment() == 'dev') {
	          	return array(
		          	'code' => 'ERR_INVALID_QUERY',
		          	'title' => 'Données transmises invalides',
                    'message' => 'Les données envoyées au formulaire sont invalides: '.str_replace("\n", ' ;; ', $this->form->getErrorsAsString()),
	          	);
        	}
          	return array(
	          	'code' => 'ERR_INVALID_QUERY',
	          	'title' => 'Données transmises invalides',
	          	'message' => 'Les données envoyées au formulaire sont invalides',
          	);
        }

        $woman = $this->form->getData();

        if (is_null($session->get('uid'))) {
            $session->invalidate();
            $this->step = 'identity';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Utilisateur invalide',
                'message' => "L'utilisateur n'est pas valide",
            );
        }

        if (($uid = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'))) === null) {
            $session->invalidate();
            $this->step = 'identity';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Utilisateur invalide',
                'message' => "L'utilisateur n'est pas valide",
            );            
        }
        $woman->setUser($uid);

        $uid->setAddress($woman->getAddress());

        $em = $this->getDoctrine()->getManager();

        foreach ($woman->getMovies() as $movie) {
            if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findBy(array('desc_fr' => $movie), array())) === null) {
                $db = new Movie();
                $db->setName(sha1($movie));
                $db->setDescFR($movie);
                $em->persist($db);
                $em->flush();
                $em->clear();
            }
        }

        foreach ($woman->getMusics() as $music) {
            if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findBy(array('desc_fr' => $music), array())) === null) {
                $db = new Music();
                $db->setName(sha1($music));
                $db->setDescFR($music);
                $em->persist($db);
                $em->flush();
                $em->clear();
            }
        }

        foreach ($woman->getTVShows() as $show) {
            if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findBy(array('desc_fr' => $show), array())) === null) {
                $db = new TVShow();
                $db->setName(sha1($show));
                $db->setDescFR($show);
                $em->persist($db);
                $em->flush();
                $em->clear();
            }
        }

        foreach ($woman->getVIP() as $vip) {
            if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findBy(array('desc_fr' => $vip), array())) === null) {
                $db = new VIP();
                $db->setName(sha1($vip));
                $db->setDescFR($vip);
                $em->persist($db);
                $em->flush();
                $em->clear();
            }
        }

        $em->persist($woman);
        $em->flush();
        $em->clear();

        // Generation de l'URL de validation
        $tempUrl = new TemporaryUrl();
        $tempUrl->setUser($uid->getId());
        //$tempUrl->setUser($uid);
        $url = $this->generateUrl('confirm', array(
            'hash' => sha1($uid->getEmail()
                    .$uid->getUsername()
                    .$uid->getLastname()
                    .$uid->getFirstname()
                    .$uid->getPassword()
                    .$this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($uid)->getType()->getName()
                    .md5(rand())
                )
            ),
            true
        );
        $tempUrl->setUrl($url);
        $tempUrl->setExpires(time()+$this->container->getParameter('expires'));
        $em->persist($tempUrl);
        $em->flush();
        $em->clear();

        // Envoi de l'email de confirmation
        $message = \Swift_Message::newInstance()
            ->setSubject('MommyVille :: Validation de votre inscription')
            ->setPriority(2)
            ->setFrom(array('inscription@mommyville.fr' => 'Inscription MommyVille'))
            ->setTo($uid->getEmail())
            // Give it a body
            ->setBody($this->renderView('MommySecurityBundle:Email:signup-email.txt.twig', array(
                'lastname' => $uid->getLastname(),
                'firstname' => $uid->getFirstname(),
                'url' => $tempUrl->getUrl(),
            )))
            // And optionally an alternative body
            ->addPart($this->renderView('MommySecurityBundle:Email:signup-email.html.twig', array(
                'lastname' => $uid->getLastname(),
                'firstname' => $uid->getFirstname(),
                'url' => $tempUrl->getUrl(),
            )), 'text/html');
        $this->get('mailer')->send($message);

        $message = \Swift_Message::newInstance()
            ->setSubject('MommyVille :: Nouvel inscrit')
            ->setPriority(2)
            ->setFrom(array('inscription@mommyville.fr' => 'Inscription MommyVille'))
            ->setTo($this->container->getParameter('contact'))
            ->setCc($this->container->getParameter('webmaster'))
            ->setBody($this->renderView('MommySecurityBundle:Email:notice-newuser.txt.twig', array(
                'lastname' => $uid->getLastname(),
                'firstname' => $uid->getFirstname(),
                'email' => $uid->getEmail(),
                'type' => $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($uid)->getType()->getName(),
                'address' => (is_object($uid->getAddress()) ? $uid->getAddress()->getLiteral() : ''),
            )));
        $this->get('mailer')->send($message);

        MommyStatsBundle::logNewUser();

        $this->step = null;

        return array(
        	'code' => 'ERR_THANKS',
            'title' => 'Merci',
        	'message' => 'Merci de vous être inscrite. Un email vous a été envoyé pour valider votre création de compte.',
        	'data' => null,
        );
    }
}