<?php

namespace Mommy\SecurityBundle\Handler;
 
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class AuthenticationHandler extends ContainerAware implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    private $router;
    private $session;
 
    /**
     * Constructor
     *
     * @author     Joe Sexton <joe@webtipblog.com>
     * @param     RouterInterface $router
     * @param     Session $session
     */
    public function __construct( RouterInterface $router, Session $session )
    {
        $this->router  = $router;
        $this->session = $session;
    }
 
    /**
     * onAuthenticationSuccess
      *
     * @author     Joe Sexton <joe@webtipblog.com>
     * @param     Request $request
     * @param     TokenInterface $token
     * @return     Response
     */
    public function onAuthenticationSuccess( Request $request, TokenInterface $token )
    {
        $token->getUser()->setLoginTime(new \DateTime());
        $session = $request->getSession();
        $session->set('_is_authenticated', true);
        $session->set('_uid', $token->getUser()->getId());
        $this->container->get('doctrine')->getManager()->flush();
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
             return new JsonResponse(array( 'success' => true ));
        // if form login 
        } else {
             if ( $this->session->get('_security.main.target_path' ) ) {
                return new RedirectResponse( $this->session->get( '_security.main.target_path' ));
            } else {
                return new RedirectResponse( $this->router->generate( 'login' ));
            } // end if
        }
    }
 
    /**
     * onAuthenticationFailure
     *
     * @author     Joe Sexton <joe@webtipblog.com>
     * @param     Request $request
     * @param     AuthenticationException $exception
     * @return     Response
     */
     public function onAuthenticationFailure( Request $request, AuthenticationException $exception )
    {
        $session = $request->getSession();
        $session->set('_is_authenticated', false);
        // if AJAX login
        if ( $request->isXmlHttpRequest() ) {
            return new JsonResponse(array( 'success' => false, 'message' => $exception->getMessage() ));
        // if form login 
        } else {
            // set authentication exception to session
            $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);
            return new RedirectResponse( $this->router->generate( 'login' ) );
        }
    }
}
