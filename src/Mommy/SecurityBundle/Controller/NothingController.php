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
use Mommy\SecurityBundle\Form\NothingForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class NothingController extends Controller implements ContainerAwareInterface
{
	private $step = 'rien';

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
			$this->form = $this->createForm(new NothingForm($this->getDoctrine()->getManager(), $this->container));
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

        if (is_null($session->get('uid'))) {
            $session->invalidate();
            $this->step = 'identity';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Utilisateur invalide',
                'message' => "L'utilisateur n'est pas valide",
            );
        }

        $param = $this->form->getData();
        $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'));
        $data = array(
          'email' => $user->getEmail(),
          'lastname' => $user->getLastname(),
          'firstname' => $user->getFirstname(),
          'message' => $param['message'],
          'keep' => ($param['keep'] == true),
        );

        $message = \Swift_Message::newInstance()
          ->setSubject('MommyVille :: Personne qui n\'a rien à faire là')
          ->setPriority(2)
          ->setFrom(array('inscription@mommyville.fr' => 'Inscription MommyVille'))
          ->setTo($this->container->getParameter('contact'))
          // Give it a body
          ->setBody($this->renderView('MommySecurityBundle:Email:signup-rien.txt.twig', $data))
          // And optionally an alternative body
          ->addPart($this->renderView('MommySecurityBundle:Email:signup-rien.html.twig', $data), 'text/html');
        $this->get('mailer')->send($message);

        $this->step = null;

        return array(
        	'code' => 'ERR_THANKS',
        	'title' => 'Merci',
          'message' => 'Merci de vous être inscrit. Nous prendrons contact avec vous sous peu.',
        	'data' => null,
        );
    }
}