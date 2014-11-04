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
use Mommy\SecurityBundle\Form\AdoptForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class AdoptController extends Controller implements ContainerAwareInterface
{
	private $step = 'adoptante';

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
        return $choices;
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new AdoptForm($this->getDoctrine()->getManager(), $this->container));
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

        $adopt = $this->form->getData(); 

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
        $adopt->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($adopt);
        $em->flush();
        $em->clear();

        if ($adopt->getChildren() || $adopt->getArrived())
            $this->step = 'famille';
        else
            $this->step = 'femme';

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Adoption créée',
        	'message' => $adopt->getId(),
        );
    }
}