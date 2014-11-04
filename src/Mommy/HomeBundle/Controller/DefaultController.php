<?php

namespace Mommy\HomeBundle\Controller;

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

// User
use Mommy\SecurityBundle\Entity\User;

// Form
use Mommy\ProfilBundle\Form\MessageForm;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/display.json", name="home-display-json")
   */
  public function voirDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'home',
        'html' => '/home/',
        'menu' => 'refresh',
        'notification' => 'refresh',
        'empty' => true,
      ),
      "1" => array(
        'frame' => '#left',
        'name' => 'search',
        'html' => '/recherche/',
        'empty' => true,
      ),
/*      
      "2" => array(
        'frame' => '#left',
        'name' => 'ask',
        'html' => '/ask/dernieres',
        'empty' => false,
      ),
*/
      "3" => array(
        'frame' => '#left',
        'name' => 'club',
        'html' => '/club/derniers',
        'empty' => false,
      ),
/*
      "4" => array(
        'frame' => '#left',
        'name' => 'sondage',
        'html' => '/sondage/derniers',
        'empty' => false,
      ),
*/
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/", name="home-index")
   * @Template
   */
  public function voirAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (!is_object($session->get('_user')))
      return $this->forward('MommySecurityBundle:Default:loginDisplay', array());

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));
    $messages = array();
    $likes = array();
    $comments = array();
    $previews = array();

    $msg=array();
    $clubs = $this->getDoctrine()->getRepository('MommyClubBundle:Member')->findByMember($user);
    foreach ($clubs as $club) {
      $msg = array_merge($msg, $this->getDoctrine()->getRepository('MommyClubBundle:Message')
        ->createQueryBuilder('l')
        ->where("l.wall = :club")
        ->orderBy('l.date', 'DESC')
        ->setParameters(array(
          'club' => $club->getClub()->getId(),
          )
        )
        ->setFirstResult(0)
        ->setMaxResults(10)
        ->getQuery()
        ->getResult());
    }
    foreach ($msg as $m) {
      $messages[$m->getDate(true)] = $m;
      $comments[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:Comment')->findBy(array('message' => $m), array('date' => 'ASC'));
      $likes[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyClubBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $m->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($m->getLink())
        $previews[$m->getDate(true)] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($m->getLink());
    }

    /*
    $msg=array();
    $pros = $this->getDoctrine()->getRepository('MommyPageBundle:Like')->findByUser($user);
    foreach ($pros as $pro) {
      $msg = $this->getDoctrine()->getRepository('MommyPageBundle:Message')->findByWall($pro);
      arsort($msg);
      $msg = array_slice($msg, 0, 10, true);
    }
    foreach ($msg as $m) {
      $messages[$m->getDate(true)] = $m;
      $comments[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyPageBundle:Comment')->findBy(array('message' => $m), array('date' => 'ASC'));
      $likes[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyPageBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $m->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($m->getLink())
        $previews[$m->getDate(true)] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($m->getLink());
    }
    */

    $msg = $this->getDoctrine()->getRepository('MommyProfilBundle:Message')
      ->createQueryBuilder('l')
      ->where("l.author = :user OR l.wall = :user")
      ->orderBy('l.date', 'DESC')
      ->setParameters(array(
        'user' => $user->getId(),
        )
      )
      ->setFirstResult(0)
      ->setMaxResults(10)
      ->getQuery()
      ->getResult();
    foreach ($msg as $m) {
      $messages[$m->getDate(true)] = $m;
      $comments[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyProfilBundle:Comment')->findBy(array('message' => $m), array('date' => 'ASC'));
      $likes[$m->getDate(true)][$m->getId()] = $this->getDoctrine()->getRepository('MommyProfilBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $m->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($m->getLink())
        $previews[$m->getDate(true)] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($m->getLink());
    }

    arsort($messages);

    return array(
      'form' => $form->createView(),
      'messages' => $messages, 
      'likes' => $likes, 
      'comments' => $comments,
      'action' => $this->generateUrl('profil-post', array()),
      );
  }
}