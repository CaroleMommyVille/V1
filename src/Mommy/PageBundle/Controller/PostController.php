<?php

namespace Mommy\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// User
use Mommy\SecurityBundle\Entity\User;

// Entities
use Mommy\PageBundle\Entity\Comment;
use Mommy\PageBundle\Entity\Message;
use Mommy\PageBundle\Entity\LikeMessage;

// Form
use Mommy\PageBundle\Form\MessageForm;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class PostController extends Controller
{
  /**
   * @Route("/post/{uid}", name="page-post", defaults={"uid" = null})
   * @Template
   */
  public function postAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $pro = $this->getDoctrine()->getRepository('MommyProfilBundle:Pro')->find($uid);

    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (!$form->isValid()) {
        return new Response('Impossible de poster le message', Response::HTTP_BAD_REQUEST);
      }
      $msg = $form->getData();
      $msg->setWall($pro);
      $msg->setAuthor($user);
      $msg->setDate();
      $em = $this->getDoctrine()->getManager();
      $em->persist($msg);
      $em->flush();
      $em->clear();

      $likes[$msg->getId()] = 0;
      $comments[$msg->getId()] = array();

      return array(
        'msg' => $msg,
        'likes' => $likes,
        'comments' => $comments,
      );
    }
  }

  /**
   * @Route("/comment/{id}", name="page-comment", requirements={"id" = ".+"})
   * @Template
   */
  public function commentAction($id) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $id = str_replace('comment-', '', $id);
    $post = $this->getDoctrine()->getRepository('MommyPageBundle:Message')->find($id);
    if (is_null($post)) {
    	return new Response("Le message correspondant n'existe pas", Response::HTTP_BAD_REQUEST);
    }
    $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $param = $request->request->get("comment");
    if (is_null($param) || empty($param) || (trim($param) == ''))
    	return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);

  	$comment = new Comment();
  	$comment->setMessage($post);
  	$comment->setDate();
  	$comment->setAuthor($current);
  	$comment->setContent($param);
    $em = $this->getDoctrine()->getManager();
    $em->persist($comment);
    $em->flush();
    $em->clear();

    return array('comment' => $comment);
  }

  /**
   * @Route("/post/like/{id}", name="page-like", requirements={"id" = ".+"})
   * @Template
   */
  public function likeAction($id) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $id = str_replace('like-', '', $id);
    $post = $this->getDoctrine()->getRepository('MommyPageBundle:Message')->find($id);
    if (is_null($post)) {
    	return new Response("Le message correspondant n'existe pas", Response::HTTP_BAD_REQUEST);
    }
    $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($like = $this->getDoctrine()->getRepository('MommyPageBundle:LikeMessage')->findOneBy(array('user' => $current, 'message' => $post), array())) === null) {
    	$like = new LikeMessage();
    	$like->setMessage($post);
    	$like->setUser($current);
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($like);
	    $em->flush();
	    $em->clear();
    }
    $like = $this->getDoctrine()->getRepository('MommyPageBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $post->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
    return array('like' => $like);
  }
}