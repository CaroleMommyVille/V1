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
use Mommy\SecurityBundle\Form\MamangeForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class MamangeController extends Controller implements ContainerAwareInterface
{
	private $step = 'mamange';

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

        $choices['ivg'] = $mem->get('profil-form-mamange-ivg');
        if (!$choices['ivg']) {
            $ivg = $mem->get('profil-mamange-ivg');
            if (is_null($ivg) || !$ivg) {
                $ivg = $this->getDoctrine()->getRepository('MommyProfilBundle:MamangeIVG')->findAll();
                $mem->set('profil-mamange-ivg', $ivg);
            }
            foreach ($ivg as $item) {
                $choices['ivg'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-ivg', $choices['ivg'], 0);
        }

        $choices['img'] = $mem->get('profil-form-mamange-img');
        if (!$choices['img']) {
            $img = $mem->get('profil-mamange-img');
            if (is_null($img) || !$img) {
                $img = $this->getDoctrine()->getRepository('MommyProfilBundle:MamangeIMG')->findAll();
                $mem->set('profil-mamange-img', $img);
            }
            foreach ($img as $item) {
                $choices['img'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-img', $choices['img'], 0);
        }

        $choices['disease'] = $mem->get('profil-form-mamange-disease');
        if (!$choices['disease']) {
            $disease = $mem->get('profil-mamange-disease');
            if (is_null($disease) || !$disease) {
                $disease = $this->getDoctrine()->getRepository('MommyProfilBundle:MamangeBaby')->findAll();
                $mem->set('profil-mamange-disease', $disease);
            }
            foreach ($disease as $item) {
                $choices['disease'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-disease', $choices['disease'], 0);
        }

        $choices['followup'] = $mem->get('profil-form-mamange-followup');
        if (!$choices['followup']) {
            $followup = $mem->get('profil-mamange-follow-up');
            if (is_null($followup) || !$followup) {
                $followup = $this->getDoctrine()->getRepository('MommyProfilBundle:MamangeBaby')->findAll();
                $mem->set('profil-mamange-follow-up', $followup);
            }
            foreach ($followup as $item) {
                $choices['followup'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-mamange-followup', $choices['followup'], 0);
        }

        $mem = null; unset($mem);
        return $choices;
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new MamangeForm($this->getDoctrine()->getManager(), $this->container));
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

        $mamange = $this->form->getData(); 

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
        $mamange->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($mamange);
        $em->flush();

        if ($mamange->getBaby() == 'mamange-baby-4')
            $this->step = 'presquenceinte';
        else
            $this->step = 'femme';

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Mamange créée',
        	'message' => $mamange->getId(),
        );
    }
}