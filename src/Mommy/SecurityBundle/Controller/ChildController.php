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
use Mommy\SecurityBundle\Form\ChildForm;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class ChildController extends Controller implements ContainerAwareInterface
{
	private $step = 'enfant';

	private $form = null;

	protected $container = null;

	public function __construct(ContainerInterface $container) {
        $this->setContainer($container);
    }

    public function setContainer(ContainerInterface $container = null) {
        if (!is_null($container)) $this->container =& $container;
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

        $choices['maternity'] = $mem->get('security-form-maternity');
        if (!$choices['maternity']) {
            $maternity = $mem->get('profil-maternity');
            if (is_null($maternity) || !$maternity) {
                $maternity = $this->getDoctrine()->getRepository('MommyProfilBundle:Maternity')->findAll();
                $mem->set('profil-maternity', $maternity);
            }
            foreach ($maternity as $m) {
                $choices['maternity'][$m->getName()] = array(
                    'text' => $m->getName(), 
                    'addr' => $m->getAddress()->getLiteral(),
                );
            }
            $mem->set('security-form-maternity', $choices['maternity'], 0);
        }

        $choices['preparation'] = $mem->get('security-form-pregnancy-preparation');
        if (!$choices['preparation']) {
            $preparation = $mem->get('profil-pregnancy-preparation');
            if (is_null($preparation) || !$preparation) {
                $preparation = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancyPreparation')->findAll();
                $mem->set('profil-pregnancy-preparation', $preparation);
            }
            foreach ($preparation as $m) {
                $choices['preparation'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-pregnancy-preparation', $choices['preparation'], 0);
        }

        $choices['deliverymethod'] = $mem->get('security-form-delivery-method');
        if (!$choices['deliverymethod']) {
            $method = $mem->get('profil-delivery-method');
            if (is_null($method) || !$method) {
                $method = $this->getDoctrine()->getRepository('MommyProfilBundle:DeliveryMethod')->findAll();
                $mem->set('profil-delivery-method', $method);
            }
            foreach ($method as $m) {
                $choices['deliverymethod'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-delivery-method', $choices['deliverymethod'], 0);
        }

        $choices['disease'] = $mem->get('security-form-child-disease');
        if (!$choices['disease']) {
            $disease = $mem->get('profil-child-disease');
            if (is_null($disease) || !$disease) {
                $disease = $this->getDoctrine()->getRepository('MommyProfilBundle:ChildDisease')->findAll();
                $mem->set('profil-child-disease', $disease);
            }
            foreach ($disease as $m) {
                $choices['disease'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-child-disease', $choices['disease'], 0);
        }

        $choices['hobby'] = $mem->get('security-form-child-hobby');
        if (!$choices['hobby']) {
            $hobby = $mem->get('profil-child-hobby');
            if (is_null($hobby) || !$hobby) {
                $hobby = $this->getDoctrine()->getRepository('MommyProfilBundle:ChildHobby')->findAll();
                $mem->set('profil-child-hobby', $hobby);
            }
            foreach ($hobby as $m) {
                $choices['hobby'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-child-hobby', $choices['hobby'], 0);
        }

        $choices['sport'] = $mem->get('security-form-child-sport');
        if (!$choices['sport']) {
            $sport = $mem->get('profil-child-sport');
            if (is_null($sport) || !$sport) {
                $sport = $this->getDoctrine()->getRepository('MommyProfilBundle:ChildSport')->findAll();
                $mem->set('profil-child-sport', $sport);
            }
            foreach ($sport as $m) {
                $choices['sport'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-child-sport', $choices['sport'], 0);
        }

        $choices['pathogro'] = $mem->get('security-form-pathology-pregnancy');
        if (!$choices['pathogro']) {
            $pathogro = $mem->get('profil-pathology-pregnancy');
            if (is_null($pathogro) || !$pathogro) {
                $pathogro = $this->getDoctrine()->getRepository('MommyProfilBundle:PathologyPregnancy')->findAll();
                $mem->set('profil-pathology-pregnancy', $pathogro);
            }
            foreach ($pathogro as $m) {
                $choices['pathogro'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-pathology-pregnancy', $choices['pathogro'], 0);
        }

        $choices['pathobb'] = $mem->get('security-form-pathology-baby');
        if (!$choices['pathobb']) {
            $pathobb = $mem->get('profil-pathology-baby');
            if (is_null($pathobb) || !$pathobb) {
                $pathobb = $this->getDoctrine()->getRepository('MommyProfilBundle:PathologyBaby')->findAll();
                $mem->set('profil-pathology-baby', $pathobb);
            }
            foreach ($pathobb as $m) {
                $choices['pathobb'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-pathology-baby', $choices['pathobb'], 0);
        }

        $choices['symptoms'] = $mem->get('security-form-pregnancy-symptoms');
        if (!$choices['symptoms']) {
            $symptoms = $mem->get('profil-pregnancy-symptoms');
            if (is_null($symptoms) || !$symptoms) {
                $symptoms = $this->getDoctrine()->getRepository('MommyProfilBundle:PregnancySymptoms')->findAll();
                $mem->set('profil-pregnancy-symptoms', $symptoms);
            }
            foreach ($symptoms as $m) {
                $choices['symptoms'][$m->getName()] = $m->getDescFR();
            }
            $mem->set('security-form-pregnancy-symptoms', $choices['symptoms'], 0);
        }

        $mem = null; unset($mem);

        $choices['more'] = false;
        if (($uid = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'))) !== null) {
            $family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($uid);
            $size = $this->getDoctrine()->getRepository('MommyProfilBundle:Child')->createQueryBuilder('l')->select('COUNT(l)')->where('l.user = :uid')->setParameter('uid', $uid->getId())->getQuery()->getSingleScalarResult();
            if (!is_null($family)) {
                if (is_object($family->getSize())) {
                    if (($family->getSize()->getName() == 'nbenfants-plus') && ($size <= 3)) {
                        $choices['more'] = true;
                    } else if (($family->getSize()->getName() == 'nbenfants-triples') && ($size <= 2)) {
                        $choices['more'] = true;
                    } else if (($family->getSize()->getName() == 'nbenfants-trois') && ($size <= 2)) {
                        $choices['more'] = true;
                    } else if (($family->getSize()->getName() == 'nbenfants-jumeaux') && ($size <= 1)) {
                        $choices['more'] = true;
                    } else if (($family->getSize()->getName() == 'nbenfants-deux') && ($size <= 1)) {
                        $choices['more'] = true;
                    }
                }
            }
        }

        $choices['nb'] = $size+1;

        return $choices;
    }

	public function getForm() {
 		if (is_null($this->form))
			$this->form = $this->createForm(new ChildForm($this->getDoctrine()->getManager(), $this->container));
		return $this->form;
	}

	public function getFormView() {
		if (is_null($this->form)) $this->getForm();
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

        $child = $this->form->getData(); 

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
        $child->setMother($uid);

        $em = $this->getDoctrine()->getManager();
        $em->merge($child);
        $em->flush();
        $em->clear();

        $more = false;
        $family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($uid);
        $size = $this->getDoctrine()->getRepository('MommyProfilBundle:Child')->createQueryBuilder('l')->select('COUNT(l)')->where('l.user = :uid')->setParameter('uid', $uid->getId())->getQuery()->getSingleScalarResult();
        if (!is_null($family)) {
            if (is_object($family->getSize())) {
                if (($family->getSize()->getName() == 'nbenfants-plus') && ($size <= 3)) {
                    $more = true;
                } else if (($family->getSize()->getName() == 'nbenfants-triples') && ($size <= 2)) {
                    $more = true;
                } else if (($family->getSize()->getName() == 'nbenfants-trois') && ($size <= 2)) {
                    $more = true;
                } else if (($family->getSize()->getName() == 'nbenfants-jumeaux') && ($size <= 1)) {
                    $more = true;
                } else if (($family->getSize()->getName() == 'nbenfants-deux') && ($size <= 1)) {
                    $more = true;
                }
            }
        }

        if ($more) {
            /*
            if ($this->form->get('more')->getData() == 'oui') {
                $this->step = 'enfant';
            } else if ($this->form->get('more')->getData() == 'non') {
                $this->step = 'femme';
            } else {
                */
                $family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid')));
                switch ($family->getSize()->getName()) {
                    case 'nbenfants-un':
                        $this->step = 'femme';
                        break;
                    case 'nbenfants-deux':
                        if ($size > 1)
                            $this->step = 'femme';
                        else
                            $this->step = 'enfant';
                        break;
                    case 'nbenfants-trois':
                        if ($size > 2)
                            $this->step = 'femme';
                        else
                            $this->step = 'enfant';
                        break;
                    case 'nbenfants-plus':
                        if ($size > 3)
                            $this->step = 'femme';
                        else
                            $this->step = 'enfant';
                        break;
                    case 'nbenfants-jumeaux':
                        if ($size > 1)
                            $this->step = 'femme';
                        else
                            $this->step = 'enfant';
                        break;
                    case 'nbenfants-triples':
                        if ($size > 2)
                            $this->step = 'femme';
                        else
                            $this->step = 'enfant';
                        break;
                    default:
                        $this->step = 'femme';
                        break;
                }
            //}
        } else {
            $this->step = 'femme';
        }

        return array(
        	'code' => 'ERR_OK',
        	'title' => 'Enfant créé',
        	'message' => $child->getId(),
        );
    }
}