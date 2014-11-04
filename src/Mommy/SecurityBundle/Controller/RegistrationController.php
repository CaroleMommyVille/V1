<?php

namespace Mommy\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
// Session
use Symfony\Component\HttpFoundation\Session\Session;
// Statistics
use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class RegistrationController extends Controller {

    /**
     * @Route("/register/display.json", name="security-register-display-json")
     */
    public function registerDisplayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'registration',
                'html' => '/login/register',
                'empty' => true,
            )
        );
        return new JsonResponse($display);
    }

    /**
     * @Route("/register/reset/display.json", name="security-register-reset-display-json")
     */
    public function registerResetDisplayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'registration',
                'html' => '/login/register/reset',
                'empty' => true,
            )
        );
        return new JsonResponse($display);
    }

    /**
     * @Route("/register/remember/display.json", name="security-register-remember-display-json")
     */
    public function registerRememberDisplayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'registration',
                'html' => '/login/register/remember',
                'empty' => true,
            )
        );
        return new JsonResponse($display);
    }

    /**
     * @Route("/register/remember/{uid}", name="security-register-remember", defaults={"uid" = null})
     * @Template("MommySecurityBundle:Registration:register.html.twig")
     */
    public function rememberAction($uid) {
        $register = array();
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();

        if (($uid != '') && ($uid !== null)) {
            $id = base64_decode($uid . '=');
            list($uid, $step) = explode('-', $id);
            $session->set('uid', $uid);
            $session->set('registration', array('step' => $step));
        } elseif (!is_null($session->get('uid')) && (($user = $this->getDoctrine()->getRepository("MommySecurityBundle:User")->find($session->get('uid'))) !== null)) {
            $registration = $session->get('registration');
            $id = base64_encode($session->get('uid') . '-' . $registration['step']);
            $id = substr($id, 0, -1);
            $message = \Swift_Message::newInstance()
                    ->setSubject('MommyVille :: Venez finir votre inscription')
                    ->setFrom('inscription@mommyville.fr')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('MommySecurityBundle:Email:signup-remember.txt.twig', array('id' => $id, 'firstname' => $user->getFirstname())))
                    ->addPart($this->renderView('MommySecurityBundle:Email:signup-remember.html.twig', array('id' => $id, 'firstname' => $user->getFirstname())), 'text/html');
            $this->get('mailer')->send($message);
            $register = $this->registerAction();
        }
        //return $this->forward('MommySecurityBundle:Registration:register', $register);
        return $register;
    }

    /**
     * @Route("/register/reset", name="security-register-reset")
     * @Template
     */
    public function resetAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if (!is_null($session->get('uid'))) {
            $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('uid'));
            if (!is_null($user)) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
                $em->clear();
            }
        }
        $session->invalidate();
        return $this->forward('MommySecurityBundle:Registration:register', array());
    }

    /**
     * @Route("/register", name="security-register-html")
     * @Template
     */
    public function registerAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();

        $error = array();
        $registration = $session->get('registration');
               
        
        if (is_null($registration)) {
            $registration = array(
                'step' => 'identity',
            );
        }

        if ($request->getMethod() == 'POST') {
            $error = $this->get("mommy.security.{$registration['step']}")->handleForm();
            $registration['step'] = $this->get("mommy.security.{$registration['step']}")->getStep();
            $session->set('registration', $registration);
        }

        if (is_null($registration['step'])) {
            $session->invalidate();
            return $this->forward('MommySecurityBundle:Default:loginForm', array('flash' => array(
                            'title' => $error['title'],
                            'message' => $error['message'],
                            'code' => $error['code'],
                            'field' => $session->get('error-field'),
            )));
        }

        $form = $this->get("mommy.security.{$registration['step']}")->getFormView();
        $options = $this->get("mommy.security.{$registration['step']}")->getOptions();

        return array('form' => $form, 'step' => $registration['step'], 'choices' => $options, 'error' => $error);
    }

}
