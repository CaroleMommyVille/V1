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
use Mommy\SecurityBundle\Form\PMAForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class PMAController extends Controller implements ContainerAwareInterface
{
	private $step = 'pma';

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
        $request = $this->get('request');
        $session = $request->getSession();
        $choices = array();
//        $mem = new \Memcached($this->container->getParameter('cache_domain'));
//        $mem->addServer($this->container->getParameter('cache_server'), $this->container->getParameter('cache_port'));
        $mem = $this->get('session');


        $choices['gyneco'] = $mem->get('profil-form-pma-gyneco');
        if (!$choices['gyneco']) {
            $gyneco = $mem->get('profil-pathology-gyneco');
            if (is_null($gyneco) || !$gyneco) {
                $gyneco = $this->getDoctrine()->getRepository('MommyProfilBundle:PathologyGyneco')->findAll();
                $mem->set('profil-pathology-gyneco', $gyneco);
            }
            foreach ($gyneco as $item) {
                $choices['gyneco'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-gyneco', $choices['gyneco'], 0);
        }

        $choices['risk'] = $mem->get('profil-form-pma-risk');
        if (!$choices['risk']) {
            $risk = $mem->get('profil-risk-factor');
            if (is_null($risk) || !$risk) {
                $risk = $this->getDoctrine()->getRepository('MommyProfilBundle:RiskFactor')->findAll();
                $mem->set('profil-risk-factor', $risk);
            }
            foreach ($risk as $item) {
                $choices['risk'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-risk', $choices['risk'], 0);
        }

        $choices['stimulator'] = $mem->get('profil-form-pma-stimulator');
        if (!$choices['stimulator']) {
            $stimulator = $mem->get('profil-ovulation-stimulator');
            if (is_null($stimulator) || !$stimulator) {
                $stimulator = $this->getDoctrine()->getRepository('MommyProfilBundle:OvulationStimulator')->findAll();
                $mem->set('profil-ovulation-stimulator', $stimulator);
            }
            foreach ($stimulator as $item) {
                $choices['stimulator'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-stimulator', $choices['stimulator'], 0);
        }

        $choices['effects'] = $mem->get('profil-form-pma-effects');
        if (!$choices['effects']) {
            $effects = $mem->get('profil-pump-side-effects');
            if (is_null($effects) || !$effects) {
                $effects = $this->getDoctrine()->getRepository('MommyProfilBundle:PumpSideEffect')->findAll();
                $mem->set('profil-pump-side-effects', $effects);
            }
            foreach ($effects as $item) {
                $choices['effects'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-effects', $choices['effects'], 0);
        }

        $choices['methods'] = $mem->get('profil-form-pma-methods');
        if (!$choices['methods']) {
            $methods = $mem->get('profil-soft-methods');
            if (is_null($methods) || !$methods) {
                $methods = $this->getDoctrine()->getRepository('MommyProfilBundle:SoftMethod')->findAll();
                $mem->set('profil-soft-methods', $methods);
            }
            foreach ($methods as $item) {
                $choices['methods'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-methods', $choices['methods'], 0);
        }

        $choices['technics'] = $mem->get('profil-form-pma-technics');
        if (!$choices['technics']) {
            $technics = $mem->get('profil-tech-pma');
            if (is_null($technics) || !$technics) {
                $technics = $this->getDoctrine()->getRepository('MommyProfilBundle:TechPMA')->findAll();
                $mem->set('profil-tech-pma', $technics);
            }
            foreach ($technics as $item) {
                $choices['technics'][$item->getName()] = $item->getDescFR();
            }
            $mem->set('profil-form-pma-technics', $choices['technics'], 0);
        }

    if (($almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid')))) !== null) {
            $choices['since'] = true;
        } else {
            $choices['since'] = false;
        }

        $mem = null; unset($mem);
        return $choices;
    }

	public function getForm() {
		if (is_null($this->form))
			$this->form = $this->createForm(new PMAForm($this->getDoctrine()->getManager(), $this->container));
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

        $pma = $this->form->getData(); 

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
        $pma->setUser($uid);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pma);
        $em->flush();
        $em->clear();

        if ($pma->getPregnant())
            $this->step = 'enceinte';
        else
            $this->step = 'femme';

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'PMA créée',
        	'message' => $pma->getId(),
        );
    }
}
