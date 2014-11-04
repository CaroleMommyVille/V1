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
use Mommy\SecurityBundle\Form\ProjectForm;

// BirthPlan
use Mommy\ProfilBundle\Entity\BirthPlan;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class ProjectController extends Controller implements ContainerAwareInterface
{
	private $step = 'projet';

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
			$this->form = $this->createForm(new ProjectForm($this->getDoctrine()->getManager(), $this->container));
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

        $choices['breastfed'] = $mem->get('security-form-project-breastfed');
        if (!$choices['breastfed']) {
            $choices['breastfed'] = array();
            $breastfed = $mem->get('profil-child-breastfed');
            if (is_null($breastfed) || !$breastfed) {
                $breastfed = $this->getDoctrine()->getRepository('MommyProfilBundle:ChildBreastfed')->findAll();
                $mem->set('profil-child-breastfed', $breastfed);
            }
            foreach ($breastfed as $b) {
                if ($b->getName() == 'breastfed-essai') continue;
                $choices['breastfed'][$b->getName()] = $b->getDescFR();
            }
            $mem->set('security-form-project-breastfed', $choices['breastfed'], 0);
        }

        $choices['maternityfound'] = $mem->get('security-form-project-maternity-found');
        if (!$choices['maternityfound']) {
            $choices['maternityfound'] = array();
            $maternity_found = $mem->get('profil-maternity-found');
            if (is_null($maternity_found) || !$maternity_found) {
                $maternity_found = $this->getDoctrine()->getRepository('MommyProfilBundle:MaternityFound')->findAll();
                $mem->set('profil-maternity-found', $breastfed);
            }
            foreach ($maternity_found as $m) {
                $choices['maternityfound'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-project-maternity-found', $choices['maternityfound'], 0);
        }

        $choices['preparation'] = $mem->get('security-form-project-preparation');
        if (!$choices['preparation']) {
            $choices['preparation'] = array();
            $preparation = $mem->get('profil-pregnancy-preparation');
            if (is_null($preparation) || !$preparation) {
                $preparation = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancyPreparation')->findAll(array('enabled' => true));
                $mem->set('profil-pregnancy-preparation', $preparation);
            }
            foreach ($preparation as $p) {
                $choices['preparation'][$p->getName()] = $p->getDescFR();
            }
            $mem->set('security-form-project-preparation', $choices['preparation'], 0);
        }

        $choices['method'] = $mem->get('security-form-project-delivery-method');
        if (!$choices['method']) {
            $choices['method'] = array();
            $delivery_method = $mem->get('profil-delivery-method');
            if (is_null($delivery_method) || !$delivery_method) {
                $delivery_method = $this->getDoctrine()->getRepository('MommyProfilBundle:DeliveryMethod')->findAll(array('enabled' => true));
                $mem->set('profil-delivery-method', $delivery_method);
            }
            foreach ($delivery_method as $p) {
                $choices['method'][$p->getName()] = $p->getDescFR();
            }
            $mem->set('security-form-project-delivery-method', $choices['method'], 0);
        }

        $choices['food'] = $mem->get('security-form-project-food');
        if (!$choices['food']) {
            $choices['food'] = array();
            $food = $mem->get('profil-pregnancy-food');
            if (is_null($food) || !$food) {
                $food = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancyFood')->findAll(array('enabled' => true));
                $mem->set('profil-pregnancy-food', $food);
            }
            foreach ($food as $f) {
                $choices['food'][$f->getName()] = $f->getDescFR();
            }
            $mem->set('security-form-project-food', $choices['food'], 0);
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

        if (($uid = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'))) === null) {
            $session->invalidate();
            $this->step = 'identity';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Utilisateur invalide',
                'message' => "L'utilisateur n'est pas valide",
            );            
        }

        $project = $this->form->getData();
        $project->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        $em->clear();

        if (($preg = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($uid)) === null) {
            if (($preg = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($uid)) === null) {
                $session->invalidate();
                $this->step = 'identity';
                return array(
                    'code' => 'ERR_INVALID_REQUEST',
                    'title' => 'Utilisateur invalide',
                    'message' => "L'utilisateur n'est pas valide",
                );
            }
        }

        if ($preg->getPrems())
            $this->step = 'famille';
        else
            $this->step = 'femme';

        return array(
            'code' => 'ERR_OK',
            'title' => 'Projet enregistré',
            'message' => $project->getId(),
            'data' => null,
        );
    }
}