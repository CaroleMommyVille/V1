<?php

namespace Mommy\ClubBundle\Controller;

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
use Mommy\ClubBundle\Entity\Comment;
use Mommy\ClubBundle\Entity\Message;
use Mommy\ClubBundle\Entity\LikeMessage;

// Form
use Mommy\ClubBundle\Form\MessageForm;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class PostController extends Controller
{
  /**
   * @Route("/post/{cid}", name="club-post", defaults={"cid" = null})
   * @Template
   */
  public function postAction($cid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());

    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (!$form->isValid()) {
        return new Response('Impossible de poster le message', Response::HTTP_BAD_REQUEST);
      }
      $post = $form->getData();
      $club = $this->getDoctrine()->getRepository('MommyClubBundle:Club')->find($cid);
      if ($this->getDoctrine()->getRepository('MommyClubBundle:Member')->findBy(array('member' => $user, 'club' => $club), array()) === null)
        return new Response('Vous ne pouvez envoyer ce message', Response::HTTP_FORBIDDEN);

      $msg = new Message();

      $msg->setContent($post['content']);
      $msg->setWall($club);
      $msg->setAuthor($user);
      $msg->setDate();
      if (!is_null($post['preview'])) {
        $msg->setLink($post['preview']);
      }
      if (is_object($post['image']) && $post['image']->getClientSize()) {
        $url = sprintf(
          '%s%s',
          $this->container->getParameter('aws_base_url'),
          $this->getPhotoUploaderS3()->upload($post['image'])
        );
        $msg->setPhoto($url);
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($msg);
      $em->flush();
      $em->clear();

      $likes[$msg->getId()] = 0;
      $comments[$msg->getId()] = array();
      $previews = array();
      if (!is_null($post['preview'])) {
        $previews[$msg->getId()] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($post['preview']);
      }

      return array(
        'msg' => $msg,
        'likes' => $likes,
        'comments' => $comments,
        'previews' => $previews,
        'full' => true,
      );
    }
  }

  /**
   * @Route("/comment/{id}", name="club-comment", requirements={"id" = ".+"})
   * @Template
   */
  public function commentAction($id) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $id = str_replace('comment-', '', $id);
    $post = $this->getDoctrine()->getRepository('MommyClubBundle:Message')->find($id);
    if (is_null($post)) {
    	return new Response("Le message correspondant n'existe pas", Response::HTTP_BAD_REQUEST);
    }
    $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if ($current != $post->getAuthor()) {
    	if ($this->getDoctrine()->getRepository('MommyClubBundle:Member')->findBy(array('member' => $user, 'club' => $club), array()) === null)
        return new Response('Vous ne pouvez envoyer ce commentaire', Response::HTTP_FORBIDDEN);
    }
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
   * @Route("/like/{id}", name="club-like", requirements={"id" = ".+"})
   * @Template
   */
  public function likeAction($id) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $id = str_replace('like-', '', $id);
    $post = $this->getDoctrine()->getRepository('MommyClubBundle:Message')->find($id);
    if (is_null($post)) {
    	return new Response("Le message correspondant n'existe pas", Response::HTTP_BAD_REQUEST);
    }
    $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($like = $this->getDoctrine()->getRepository('MommyClubBundle:LikeMessage')->findOneBy(array('user' => $current, 'message' => $post), array())) === null) {
    	$like = new LikeMessage();
    	$like->setMessage($post);
    	$like->setUser($current);
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($like);
	    $em->flush();
	    $em->clear();
    }
    $like = $this->getDoctrine()->getRepository('MommyClubBundle:LikeMessage')
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

  protected function getPhotoUploaderS3() {
    return $this->get('amazonS3.photo_uploader');
  }

  protected function getPhotoUploaderHelios() {
    return $this->get('helios.photo_uploader');
  }
}