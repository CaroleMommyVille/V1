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
use Mommy\SecurityBundle\Form\IdentityForm;

// User
use Mommy\SecurityBundle\Entity\User;
use Mommy\ProfilBundle\Entity\Friend;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class IdentityController extends Controller implements ContainerAwareInterface
{
	private $step = 'identity';

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
        return array();
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new IdentityForm($this->getDoctrine()->getManager(), $this->container));
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

        $user = $this->form->getData(); 

        if (!$user->getCnil() || is_null($user->getCnil())) {
            $session->set('error-msg', "Vous n'avez pas accepté nos CGU");
            $session->set('error-field', 'identity_cnil');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Nos CGU ?',
                'message' => "Vous n'avez pas accepté nos CGU",
            );
        }

        $user->disableAccount();
        $user->setSince('now');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        if (($carole = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneByEmail('carole@mommyville.fr')) !== null) {
            $friend = new Friend();
            $friend->setSource($carole);
            $friend->setDest($user);
            $friend->setEnabled(true);
            $em->persist($friend);
        }

        $em->flush();
        $em->clear();

        $session->set('uid', $user->getId());

        $this->step = 'type';

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Utilisateur créé',
        	'message' => $user->getId(),
        );
    }
}