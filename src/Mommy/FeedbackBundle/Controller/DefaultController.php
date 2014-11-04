<?php

namespace Mommy\FeedbackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mommy_feedback")
     * @Template
     */
    public function indexAction()
    {
        MommyUIBundle::logStatistics($this->get('request'));
        return array();
    }

    /**
     * @Route("/html", name="feedback-html")
     * @Template
     */
    public function htmlAction()
    {
        MommyUIBundle::logStatistics($this->get('request'));
    	$form = $this->createFormBuilder(null, array(
            'csrf_protection' => false,
            'attr' => array(
                'id' => 'feedback',
                )
            ))
    		->add('message', 'textarea', array(
                'required' => true,
                'label' => 'Votre message',
    			)
    		)
    		->add('send', 'submit', array(
    			'label' => 'Envoyer',
    			'attr' => array(
    				'class' => 'btn-style',
    				)
    			)
    		)
    		->setAction($this->generateUrl('feedback-html'))
            ->setMethod('POST')
            ->getForm();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $msg = $form->get('message')->getData();
                $server = (is_object($request->server) ? var_export($request->server->all(), true) : null);
                $request = (is_object($request->request) ? var_export($request->request->all(), true) : null);
//                $query = (is_object($request->query) ? var_export($request->query->all(), true) : null);
                $query = null;
                $user = var_export($this->get('security.context')->getToken()->getUser(), true);
                if (is_object($request->getSession()))
                    $session = $request->getSession()->get('_user');
                else
                    $session = null;

                // Envoi de l'email de confirmation
                $email = \Swift_Message::newInstance()
                  ->setSubject('MommyVille :: Feedback')
                  ->setFrom(array('site@mommyville.fr' => 'MommyVille Feddback'))
                  ->setTo($this->container->getParameter('webmaster'))
                  // Give it a body
                  ->setBody($this->renderView('MommyFeedbackBundle:Default:feedback-email.txt.twig', array(
                    'msg' => $msg,
                    'server' => $server,
                    'get' => $query,
                    'post' => $request,
                    'user' => $user,
                  )));
                $this->get('mailer')->send($email);
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/display.json", name="feedback-display-json")
     */
    public function displayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#footerSlideText',
                'name' => 'feedback',
                'html' => '/feedback/html',
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }
}