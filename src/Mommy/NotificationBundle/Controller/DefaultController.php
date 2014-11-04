<?php

namespace Mommy\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/display.json", name="notification-display-json")
   */
  public function voirDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#notification',
        'name' => 'notification',
        'html' => '/notification',
        'empty' => true,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/", name="notification-index")
   * @Template
   */
  public function indexAction() {
  	MommyUIBundle::logStatistics($this->get('request'));
  	$request = $this->get('request');
  	$user = $user = $this->get('security.context')->getToken()->getUser();

    $new = 0;
    $friends = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend')->findBy(array('dest' => $user, 'enabled' => false), array());
    $new += sizeof($friends);

    $nets = $this->getDoctrine()->getRepository('MommyProfilBundle:Network')->findBy(array('dest' => $user, 'seen' => false), array());
    foreach ($nets as $net) {
      $net->setSeen(true);
    }
    $new += sizeof($nets);

    $em = $this->getDoctrine()->getManager();
    $em->flush();
    $em->clear();

    return array(
      'friends' => $friends,
      'network' => $nets,
      'nbnote' => $new,
      );
  }
}