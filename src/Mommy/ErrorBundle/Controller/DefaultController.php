<?php

namespace Mommy\ErrorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
	private $errors = array(
		'ERR_OK'                => array('http' => 200, 'title' => 'OK', 'msg' => 'Aucune erreur détectée.'),
		'ERR_NOT_CONNECTED'     => array('http' => 403, 'title' => 'Non connecté', 'msg' => 'Vous n\'êtes actuellement pas connecté.'),
		'ERR_INVALID_USER'      => array('http' => 400, 'title' => 'Utilisateur invalide', 'msg' => 'L\'utilisateur auquel vous voulez accéder est invalide.'),
        'ERR_EXISTING_USER'     => array('http' => 403, 'title' => 'Déjà enregistré', 'msg' => 'Cet utilisateur déjà enregistré.'),
        'ERR_INVALID_CHILD'     => array('http' => 400, 'title' => 'Enregistrement \'Enfant\' non valide ', 'msg' => 'L\'enregistrement \'Enfant\' auquel vous voulez accédere n\'est pas invalide.'),
        'ERR_INVALID_REQUEST'   => array('http' => 400, 'title' => 'Requête invalide', 'msg' => 'La requête exécutée ne semble pas invalide.'),
        'ERR_INVALID_QUERY'     => array('http' => 400, 'title' => 'Demande invalide', 'msg' => 'La demande envoyée n\'est pas valide.'),
        'ERR_INVALID_QUESTION'  => array('http' => 400, 'title' => 'Question invalide', 'msg' => 'La question posée n\'est pas valide.'),
        'ERR_INVALID_AD'        => array('http' => 400, 'title' => 'Pub invalide', 'msg' => 'La publicité n\'est pas valide.'),
        'ERR_PERMISSION_DENIED' => array('http' => 403, 'title' => 'Action non autorisée', 'msg' => 'L\'action demandée n\'est pas autorisée.'),
        'ERR_MUST_ANSWER'       => array('http' => 400, 'title' => 'Réponse obligatoire', 'msg' => 'Cette réponse est obligatoire.'),
        'ERR_THANKS'            => array('http' => 200, 'title' => 'Merci', 'msg' => 'Merci de vous êtes inscrite.'),
        'undefined'             => array('http' => 500, 'title' => 'Inconnu', 'msg' => 'Une erreur inconnue s\'est produite.'),
	);

    /**
     * @Route("/404/display.json", name="error-404-display-json")
     */
    public function PageNotFoundDisplayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $request = $this->get('request');
        $session = $request->getSession();
        if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'error',
                'html' => '/error/404',
                'title' => 'MommyLost',
                'menu' => 'refresh',
                'notification' => 'refresh',
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }

    /**
     * @Route("/{code}.json", requirements={"code" = ".+"})
     */
    public function indexAction($code)
    {
        $request = $this->get('request');
        $session = $request->getSession();
        $param = array();
        MommyUIBundle::logStatistics($this->get('request'));
        if ($code == 'ERR_MUST_ANSWER') {
            $param = array(
                'add' => $session->get('error-msg'),
                'id' => $session->get('error-field'),
            );
            $session->set('error-msg', '');
            $session->set('error-field', '');
        }
        $param['http'] = $this->errors[$code]['http'];
        $param['title'] = $this->errors[$code]['title'];
        $param['msg'] = $this->errors[$code]['msg'];
        return new JsonResponse($param);
    }


    /**
     * @Route("/404", name="error-404")
     * Template()
     */
    public function PageNotFoundAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        return $this->render("MommyErrorBundle:Default:PageNotFound.html.twig", array());
    }
}
