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
use Mommy\SecurityBundle\Form\MotherForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class MotherController extends Controller implements ContainerAwareInterface
{
	private $step = 'maman';

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
//        $mem = new \Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->get('session');


        $choices['after'] = $mem->get('security-form-after-delivery');
        if (!$choices['after']) {
            $after = $mem->get('profil-after-delivery');
            if (is_null($after) || !$after) {
                $after = $this->getDoctrine()->getRepository('MommyProfilBundle:AfterDelivery')->findAll();
                $mem->set('profil-after-delivery', $after);
            }
            foreach ($after as $m) {
                $choices['after'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-after-delivery', $choices['after'], 0);
        }

        $mem = null; unset($mem);
        return $choices;
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new MotherForm($this->getDoctrine()->getManager(), $this->container));
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

        $maman = $this->form->getData(); 

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
        $maman->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($maman);
        $em->flush();
        $em->clear();

        $this->step = 'femme';

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Maman créée',
        	'message' => $maman->getId(),
        );
    }
}