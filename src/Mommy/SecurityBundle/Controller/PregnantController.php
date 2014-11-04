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
use Mommy\SecurityBundle\Form\PregnantForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class PregnantController extends Controller implements ContainerAwareInterface
{
	private $step = 'enceinte';

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
			$this->form = $this->createForm(new PregnantForm($this->getDoctrine()->getManager(), $this->container));
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

        $choices['vitesses'] = $mem->get('security-form-pregnant-vitesses');
        if (!$choices['vitesses']) {
            $vitesses = $mem->get('profil-pregnancy-speed');
            if (is_null($vitesses) || !$vitesses) {
                $vitesses = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancySpeed')->findAll();
                $mem->set('profil-pregnancy-speed', $vitesses);
            }
            $choices['vitesses'] = array();
            foreach ($vitesses as $vitesse) {
                $choices['vitesses'][$vitesse->getName()] = $vitesse->getDescFR();
            }
            $mem->set('security-form-pregnant-vitesses', $choices['vitesses'], 0);
        }
 
        $choices['reactions'] = $mem->get('security-form-pregnant-reactions');
        if (!$choices['reactions']) {
            $reactions = $mem->get('profil-reaction');
            if (is_null($reactions) || !$reactions) {
                $reactions = $this->getDoctrine()->getRepository('MommyProfilBundle:Reaction')->findAll();
                $mem->set('profil-reaction', $reactions);
            }
            $choices['reactions'] = array();
            foreach ($reactions as $reaction) {
                $choices['reactions'][$reaction->getName()] = $reaction->getDescFR();
            }
            $mem->set('security-form-pregnant-reactions', $choices['reactions'], 0);
        }

        $mem = null; unset($mem);
        $choices['prems'] = array('oui' => 'Oui', 'non' => 'Non');
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

        if (($uid = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'))) === null) {
            $session->invalidate();
            $this->step = 'identity';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Utilisateur invalide',
                'message' => "L'utilisateur n'est pas valide",
            );            
        }

        $pregnant = $this->form->getData();
        $pregnant->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pregnant);
        $em->flush();
        $em->clear();

        if ($pregnant->getAmenorrhee() == null) {
            $this->step = 'projet';
        } else if ($pregnant->getAmenorrhee()/4.3 < 3) {
          // premier trismestre
          $this->step = 'trim1';
        } elseif ($pregnant->getAmenorrhee()/4.3 < 6) {
          // second trismestre
          $this->step = 'trim2';
        } else {
          // troisième trismestre
          $this->step = 'trim3';
        }

        return array(
            'code' => 'ERR_OK',
            'title' => 'Grossesse enregistrée',
            'message' => $pregnant->getId(),
            'data' => null,
        );
    }
}
