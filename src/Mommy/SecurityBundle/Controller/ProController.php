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
use Mommy\SecurityBundle\Form\ProForm;

// Pro
use Mommy\ProfilBundle\Entity\Pro;
// Address
use Mommy\ProfilBundle\Entity\Address;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;
use Mommy\StatsBundle\Controller\UsersController as MommyStatsBundle;

class ProController extends Controller implements ContainerAwareInterface
{
	private $step = 'pro';

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

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new ProForm($this->getDoctrine()->getManager(), $this->container));
		return $this->form;
	}

	public function getFormView() {
		$this->getForm();
		return $this->form->createView();
	}

    public function getOptions() {
        return array();
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

        $pro = $this->form->getData();
        $em = $this->getDoctrine()->getManager();

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
        $pro->setUser($uid);

        if ($pro->getActivity() == null) {
            $session->set('error-msg', "Dans quel domaine êtes-vous ?");
            $session->set('error-field', 'pro_activity');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Votre activité ?',
                'message' => "Dans quel domaine êtes-vous ?",
            );
        }

        if ($pro->getName() == null) {
            $session->set('error-msg', "Quel est le nom de votre société ?");
            $session->set('error-field', 'pro_name');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Votre nom ?',
                'message' => "Quel est le nom de votre société ?",
            );
        }

        if ($pro->getAddress() == null) {
            $session->set('error-msg', "Quelle est votre adresse ?");
            $session->set('error-field', 'pro_address');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Votre adresse ?',
                'message' => "Quelle est votre adresse ?",
            );
        }

        if ($pro->getStations() == null) {
            $session->set('error-msg', "Quels sont les transports à proximité ?");
            $session->set('error-field', 'pro_stations');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Les transports les plus proches ?',
                'message' => "Quels sont les transports à proximité ?",
            );
        }

        if ($pro->getPhone() == null) {
            $session->set('error-msg', "Quel est votre numéro de téléphone ?");
            $session->set('error-field', 'pro_phone');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Votre téléphone ?',
                'message' => "Quel est votre numéro de téléphone ?",
            );
        }

        if ($pro->getDescription() == null) {
            $session->set('error-msg', "Donnez-nous une description de votre activité ?");
            $session->set('error-field', 'pro_description');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Une description ?',
                'message' => "Donnez-nous une description de votre activité ?",
            );
        }

        $em->persist($pro);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('MommyVille :: Nouvel inscrit - pro')
            ->setPriority(2)
            ->setFrom(array('inscription@mommyville.fr' => 'Inscription MommyVille'))
            ->setTo($this->container->getParameter('contact'))
            ->setBody($this->renderView('MommySecurityBundle:Email:notice-newpro.txt.twig', array(
                'name' => $pro->getName(),
                'activity' => $pro->getActivity()->getDescFR(),
                'address' => $pro->getAddress()->getLiteral(),
                'phone' => $pro->getPhone(),
                'description' => $pro->getDescription(),
                'opening' => $pro->getOpening(),
            )));
        $this->get('mailer')->send($message);

        MommyStatsBundle::logNewPro();

        $this->step = null;

        return array(
            'code' => 'ERR_THANKS',
            'title' => 'Merci',
            'message' => 'Merci de vous être inscrit. Nous revenons vers vous dès que votre inscription est validée.',
            'data' => null,
        );
    }
}
