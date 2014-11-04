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
use Mommy\SecurityBundle\Form\TypeForm;

// Type
use Mommy\ProfilBundle\Entity\UserType;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class TypeController extends Controller implements ContainerAwareInterface
{
	private $step = 'type';

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
			$this->form = $this->createForm(new TypeForm($this->getDoctrine()->getManager(), $this->container));
		return $this->form;
	}

	public function getFormView() {
		$this->getForm();
		return $this->form->createView();
	}

    public function getOptions() {
        $choices = array();
//        $mem = new \Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->get('session');

        $choices['types'] = $mem->get('security-form-type');
        if (!$choices['types']) {
            $types = $mem->get('profil-type');
            if (is_null($types) || !$types) {
                $types = $this->getDoctrine()->getRepository('MommyProfilBundle:Type')->findAll();
                $mem->set('profil-type', $types);
            }
            foreach ($types as $type) {
                $choices['types'][$type->getName()] = $type->getDescFR();
            }
            $mem->set('security-form-type', $choices['types'], 0);
        }

        $mem = null; unset($mem);
        return $choices;
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

        $type = $this->form->getData();

        if (!is_object($type) || !is_object($type->getType())) {
            $session->set('error-msg', "Vous n'avez pas précisé quel type de maman vous êtiez");
            $session->set('error-field', 'type_type');
            return array(
                'code' => 'ERR_MUST_ANSWER',
                'title' => 'Quel type ?',
                'message' => "Vous n'avez pas précisé quel type de maman vous êtiez",
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
        $em = $this->getDoctrine()->getManager();
        $uid->lockAccount(true);
        $em->persist($uid);
        $type->setUser($uid);
        $em->persist($type);
        $em->flush();
        $em->clear();

        $session->set('tid', $type->getId());

        $this->step = str_replace('type-', '', $type->getType()->getName());

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Type assigné',
        	'message' => $type->getId(),
        );
    }
}