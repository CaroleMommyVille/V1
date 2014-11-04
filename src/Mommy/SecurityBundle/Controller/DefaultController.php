<?php

namespace Mommy\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;

// Temporary URL
use Mommy\SecurityBundle\Entity\TemporaryUrl;

// Session
use Symfony\Component\HttpFoundation\Session\Session;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="login")
     * @Template
     */
    public function loginAction()
    {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        return array();
    }

    /**
     * @Route("/lost-password/display.json", name="login-lost-password-display.json")
     * @Template
     */
    public function lostPasswordDisplayAction(Request $request)
    {
        MommyUIBundle::logStatistics($this->get('request'));
        return new JsonResponse(array(
            "0" => array(
                'frame' => '#center',
                'name' => 'lost-password',
                'html' => '/login/lost-password',
                'empty' => true,
            ),
        ));
    }

    /**
     * @Route("/lost-password", name="login-lost-password")
     * @Template
     */
    public function lostPasswordAction()
    {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();

        $msg = '';
        $form = $this->createFormBuilder()
            ->add('email', 'email', array())
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if (($user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneByEmail($form->get('email')->getData())) === null)
                    return array(
                        'form' => $form->createView(),
                        'msg' => "Impossible de trouver ce compte",
                        );
                $tempUrl = new TemporaryUrl();
                $tempUrl->setUser($user->getId());
                $url = $this->generateUrl('login-change-password', array(
                    'hash' => sha1($user->getEmail().$user->getUsername()
                            .$user->getLastname()
                            .$user->getFirstname()
                            .$user->getPassword()
                            .$this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user)->getType()->getName()
                            .time()
                            .md5(rand())
                        )
                    ),
                    true
                );
                $tempUrl->setUrl($url);
                $tempUrl->setExpires(time()+$this->container->getParameter('expires'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($tempUrl);
                $em->flush();
                $em->clear();

                $message = \Swift_Message::newInstance()
                    ->setSubject('MommyVille :: Venez réinitialiser votre mot de passe')
                    ->setFrom(array('motdepasse@mommyville.fr' => 'Votre mot de passe MommyVille'))
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('MommySecurityBundle:Email:lost-password.txt.twig', array('url' => $url, 'firstname' => $user->getFirstname())))
                    ->addPart($this->renderView('MommySecurityBundle:Email:lost-password.html.twig', array('url' => $url, 'firstname' => $user->getFirstname())), 'text/html');
                $this->get('mailer')->send($message);
                $msg = 'Nous venons de vous envoyer un e-mail avec la procédure à suivre.';
            }
        }
        return array(
            'form'  => $form->createView(),
            'msg' => $msg,
        );
    }

    /**
     * @Route("/change-password/{hash}", name="login-change-password", requirements={"hash" = ".+"})
     * @Template
     */
    public function changePasswordAction($hash)
    {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();

        $url = $this->getDoctrine()->getRepository('MommySecurityBundle:TemporaryUrl')->findOneByUrl($this->generateUrl('login-change-password', array('hash' => $hash), true));

        if (!is_object($url)) {
            return $this->redirect('MommySecurityBundle:Default:login');
        }
            
        if ($url->getExpires() < time()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($url);
            $em->flush();
            return $this->redirect('MommySecurityBundle:Default:login');
        }            
        $form = $this->createFormBuilder()
            ->add('password', 'password', array())
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($url->getUser());
                $user->setPassword($form->get('password')->getData());

                $em = $this->getDoctrine()->getManager();
//                $em->persist($user);
                $em->remove($url);
                $em->flush();
                $em->clear();

                $token = new UsernamePasswordToken($user->getUsername(), $user->getPassword(), 'main', $user->getRoles());
                $this->get('security.context')->setToken($token);
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                return $this->redirect('MommySecurityBundle:Default:login');
            }
        }
        return array(
            'form'  => $form->createView(),
            'url' => $url->getUrl(),
        );
    }

    /**
     * @Route("/form", name="login-form")
     * @Template
     */
    public function loginFormAction($flash = null) {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('login_check'))
            ->setMethod('POST')
            ->add('username', 'email', array(
                'data' => $session->get(SecurityContext::LAST_USERNAME)
                )
            )
            ->add('password', 'password')
            ->add('remember_me', 'checkbox', array(
                'attr'      => array('checked' => 'checked'),
                'label'     => 'Keep me logged in',
                'required'  => false,
                )
            )
            ->add('login', 'submit', array(
                'label' => 'Connectez-vous',
                )
            )
            ->getForm()
            ->createView();

        return array(
            'flash' => $flash,
            'form'  => $form,
            'error' => $error,
            'last_username' => $session->get(SecurityContext::LAST_USERNAME)
        );        
    }

    /**
     * @Route("/display.json", name="login-display-json")
     */
    public function loginDisplayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if ($session->get('_is_authenticated')) {  
            $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_uid'));
            $session->set('_user', $user);
            $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user->getId());
            $session->set('_type', $type);
            switch ($type->getType()->getName()) {
                case 'rien':
                    // do not signin
                    break;
                case 'pro':
                    return $this->forward('MommyPageBundle:Default:voirDisplay', array());
                case 'enceinte':
                case 'maman':
                case 'presquenceinte':
                case 'adoptante':
                case 'mamange':
                default: 
                    //return $this->forward('MommyClubBundle:Default:voirDisplay', array());
                return $this->forward('MommyHomeBundle:Default:voirDisplay', array());
            }
        }
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'login',
                'html' => '/login/form',
                'menu' => 'refresh',
                'empty' => true,
            ),
            "1" => array(
                'frame' => '#center',
                'name' => 'registration',
                'html' => '/login/register',
                'empty' => false,
            )
        );
        return new JsonResponse($display);
    }

    /**
     * @Route("/confirm/{hash}", name="confirm", requirements={"hash" = ".+"})
     * @Template
     */
    public function confirmAction($hash, Request $request)
    {
        MommyUIBundle::logStatistics($this->get('request'));
        $session = $request->getSession();
        if (!is_string($hash))
            Throw $this->createNotFoundException('Invalid token');
        if (($url = $this->getDoctrine()->getRepository('MommySecurityBundle:TemporaryUrl')->findOneByUrl($this->generateUrl('confirm', array('hash' => $hash), true))) === null)
            return $this->forward('MommySecurityBundle:Default:login', array());

        if (!is_object($url))
            $this->redirect($this->generateUrl('login'));
        
        if ($url->getExpires() < time()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($url);
            $em->flush();
            Throw $this->createNotFoundException('Invalid token');
        }

        $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($url->getUser());
        $user->enableAccount(true);
        $user->lockAccount(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->remove($url);
        $em->flush();
        $em->clear();

//        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $token = new UsernamePasswordToken($user->getUsername(), $user->getPassword(), 'main', $user->getRoles());
        $this->get('security.context')->setToken($token);
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        return $this->forward('MommySecurityBundle:Default:login', array());
    }
}