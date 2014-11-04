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
use Mommy\SecurityBundle\Form\Trim3Form;

// Quarter3 
use Mommy\ProfilBundle\Entity\Quarter3;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class Trim3Controller extends Controller implements ContainerAwareInterface
{
	private $step = 'trim3';

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
			$this->form = $this->createForm(new Trim3Form($this->getDoctrine()->getManager(), $this->container));
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

        $choices['result'] = array();
        $results = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancyResult')->findAll();
        foreach ($results as $result) {
            $choices['result'][$result->getName()] = $result->getDescFR();
        }

        $choices['stopped'] = array('oui' => 'Je suis arrêtée', 'non' => 'Je veux continuer le plus longtemps possible');

        $choices['consult3'] = array('oui' => 'Oui', 'non' => 'Non');

        $choices['status'] = $mem->get('security-form-trim3-status');
        if (!$choices['status']) {
            $choices['status'] = array();
            $status = $mem->get('profil-pregnancy-status');
            if (is_null($status) || !$status) {
                $status = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancyStatus')->findAll();
                $mem->set('profil-pregnancy-status', $status);
            }
            foreach ($status as $s) {
                $choices['status'][$s->getName()] = $s->getDescFR();
            }
            $mem->set('security-form-trim3-status', $choices['status'], 0);
        }

        $choices['PregnancySymptoms'] = $mem->get('security-form-trim3-symptoms');
        if (!$choices['PregnancySymptoms']) {
            $choices['PregnancySymptoms'] = array();
            $symptoms = $mem->get('profil-pregnancy-symptoms');
            if (is_null($symptoms) || !$symptoms) {
                $symptoms = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancySymptoms')->findAll(array('enabled' => true));
                $mem->set('profil-pregnancy-symptoms', $symptoms);
            }
            foreach ($symptoms as $symptom) {
                $choices['PregnancySymptoms'][$symptom->getName()] = $symptom->getDescFR();
            }
            $mem->set('security-form-trim3-symptoms', $choices['PregnancySymptoms'], 0);
        }

        $choices['PathologyBaby'] = $mem->get('security-form-trim3-pathobb');
        if (!$choices['PathologyBaby']) {
            $choices['PathologyBaby'] = array();
            $pathobb = $mem->get('profil-pathology-baby');
            if (is_null($pathobb) || !$pathobb) {
                $pathobb = $this->getDoctrine()->getRepository('MommyProfilBundle:PathologyBaby')->findAll(array('enabled' => true));
                $mem->set('profil-pathology-baby', $pathobb);
            }
            foreach ($pathobb as $patho) {
                $choices['PathologyBaby'][$patho->getName()] = $patho->getDescFR();
            }
            $mem->set('security-form-trim3-pathobb', $choices['PathologyBaby'], 0);
        }

        $choices['PathologyPregnancy'] = $mem->get('security-form-trim3-pathogro');
        if (!$choices['PathologyPregnancy']) {
            $choices['PathologyPregnancy'] = array();
            $pathogro = $mem->get('profil-pathology-pregnancy');
            if (is_null($pathogro) || !$pathogro) {
                $pathogro = $this->getDoctrine()->getRepository('MommyProfilBundle:PathologyPregnancy')->findAll(array('enabled' => true));
                $mem->set('profil-pathology-pregnancy', $pathogro);
            }
            foreach ($pathogro as $patho) {
                $choices['PathologyPregnancy'][$patho->getName()] = $patho->getDescFR();
            }
            $mem->set('security-form-trim3-pathogro', $choices['PathologyPregnancy'], 0);
        }

        $choices['daycare_planned'] = array('oui' => 'Oui', 'non' => 'Non');

        $choices['daycare'] = $mem->get('security-form-trim3-daycare');
        if (!$choices['daycare']) {
            $choices['daycare'] = array();
            $daycare = $mem->get('profil-child-daycare');
            if (is_null($daycare) || !$daycare) {
                $daycare = $this->getDoctrine()->getRepository('MommyProfilBundle:ChildDaycare')->findAll();
                $mem->set('profil-child-daycare', $daycare);
            }
            foreach ($daycare as $d) {
                $choices['daycare'][$d->getName()] = $d->getDescFR();
            }
            $mem->set('security-form-trim3-daycare', $choices['daycare'], 0);
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

        if (($pid = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($uid)) === null) {
            $session->invalidate();
            $this->step = 'enceinte';
            return array(
                'code' => 'ERR_INVALID_REQUEST',
                'title' => 'Grossesse invalide',
                'message' => "Votre grossesse n'existe pas en base",
            );            
        }

        $trim3 = $this->form->getData();
        $trim3->setPregnancy($pid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($trim3);
        $em->flush();
        $em->clear();

        $this->step = 'projet';

        return array(
            'code' => 'ERR_OK',
            'title' => 'Troisième trimestre enregistré',
            'message' => $trim3->getId(),
            'data' => null,
        );
    }
}
