<?php

namespace Mommy\ProfilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oneup\UploaderBundle\Controller\UploaderController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// User
use Mommy\SecurityBundle\Entity\User;

// Entities
use Mommy\ProfilBundle\Entity\VIP;
use Mommy\ProfilBundle\Entity\Music;
use Mommy\ProfilBundle\Entity\TVShow;
use Mommy\ProfilBundle\Entity\Movie;
use Mommy\ProfilBundle\Entity\Activity;
use Mommy\ProfilBundle\Entity\Carousel;
use Mommy\ProfilBundle\Entity\CarouselPhoto;

use Mommy\ProfilBundle\Entity\Friend;
use Mommy\ProfilBundle\Entity\Network;

// Form
use Mommy\ProfilBundle\Form\MessageForm;
use Mommy\ProfilBundle\Form\HobbyForm;
use Mommy\ProfilBundle\Form\SerieForm;
use Mommy\ProfilBundle\Form\MovieForm;
use Mommy\ProfilBundle\Form\MusicForm;
use Mommy\ProfilBundle\Form\VIPForm;
use Mommy\ProfilBundle\Form\CarouselForm;
use Mommy\ProfilBundle\Form\ProfilForm;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
  /**
   * @Route("/voir/display.json", name="profil-voir-display-json")
   */
  public function voirDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => '/profil/voir',
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'family',
        'html' => '/profil/famille',
        'empty' => false,
      ),
/*
      "3" => array(
        'frame' => '#left',
        'name' => 'albums',
        'html' => '/profil/albums',
        'empty' => false,
      ),
*/
      "4" => array(
        'frame' => '#left',
        'name' => 'friends',
        'html' => '/profil/mes-amis',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/albums/display.json", name="profil-albums-display-json")
   */
  public function albumsDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => '/profil/albums',
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'family',
        'html' => '/profil/famille',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'friends',
        'html' => '/profil/mes-amis',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/amis/accepter/{uid}/display.json", name="profil-accept-display-json", requirements={"uid"=".+"})
   */
  public function acceptDisplayAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $this->acceptFriendAction($uid);
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => '/profil/voir',
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'family',
        'html' => '/profil/famille',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'friends',
        'html' => '/profil/mes-amis',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/amis/refuser/{uid}/display.json", name="profil-deny-display-json", requirements={"uid"=".+"})
   */
  public function denyDisplayAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $this->denyFriendAction($uid);
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => '/profil/voir',
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'family',
        'html' => '/profil/famille',
        'empty' => false,
      ),
      "3" => array(
        'frame' => '#left',
        'name' => 'friends',
        'html' => '/profil/mes-amis',
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/voir/{uid}/display.json", name="profil-voir-uid-display-json", requirements={"uid"=".+"})
   */
  public function voirUidDisplayAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => "/profil/voir/$uid",
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'familly',
        'html' => "/profil/famille/$uid",
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/albums/{uid}/display.json", name="profil-albums-uid-display-json", requirements={"uid"=".+"})
   */
  public function albumsUidDisplayAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => "/profil/albums/$uid",
        'title' => 'MommyProfil',
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
      "2" => array(
        'frame' => '#left',
        'name' => 'familly',
        'html' => "/profil/famille/$uid",
        'empty' => false,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/editer/display.json", name="profil-editer-display-json")
   */
  public function editerDisplayAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (!is_object($session->get('_user'))) return $this->forward('MommySecurityBundle:Default:loginDisplay', array());
    $display = array(
      "0" => array(
        'frame' => '#center',
        'name' => 'profil',
        'html' => '/profil/editer',
        'menu' => 'refresh',
        'empty' => true,
      ),
    );
    return new JsonResponse($display);
  }

  /**
   * @Route("/mes-amis", name="profil-my-friends")
   * @Template
   */
  public function myFriendsAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());

    $repo = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend');
    $users = $repo->createQueryBuilder('f')
      ->where("(f.source = :user OR f.dest = :user) AND f.enabled=1")
      ->setParameters(array(
        'user' => $user->getId(),
        )
      )
      ->setMaxResults('4')
      ->getQuery()
      ->getResult();
    $friends = array();
    foreach ($users as $u) {
      if ($u->getSource() == $user)
        $friends[] = $u->getDest();
      else 
        $friends[] = $u->getSource();
    }

    return array(
      'friends' => $friends,
      );
  }

  /**
   * @Route("/tous-mes-amis", name="profil-all-friends")
   * @Template
   */
  public function allFriendsAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());

    $repo = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend');
    $users = $repo->createQueryBuilder('f')
      ->where("(f.source = :user OR f.dest = :user) AND f.enabled=1")
      ->setParameters(array(
        'user' => $user->getId(),
        )
      )
      ->getQuery()
      ->getResult();
    $friends = array();
    foreach ($users as $u) {
      if ($u->getSource() == $user)
        $friends[] = $u->getDest();
      else 
        $friends[] = $u->getSource();
    }

    return array(
      'friends' => $friends,
      );
  }

  /**
   * @Route("/famille/{uid}", name="profil-famille", defaults={"uid" = null})
   * @Template
   */
  public function familleAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    return $this->getFamily($uid);
  }

  /**
   * @Route("/famille/{uid}/json", name="profil-famille-json", defaults={"uid" = null})
   */
  public function familleJsonAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    return new JsonResponse($this->getFamily($uid));
  }

  public function getFamily($uid) {
    $request = $this->get('request');
    $session = $request->getSession();
    if (is_null($uid)) {
      if (is_object($session->get('_user'))) {
        $uid = $session->get('_user')->getId();
        $self = true;
        $action = $this->generateUrl('profil-post', array());
      } else {
        return $this->forward('MommySecurityBundle:Default:loginForm', array());
      }
    } else {
      $self = false;
      $action = $this->generateUrl('profil-post', array())."/$uid";
    }
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneBy(array('id' => $uid, 'isActive' => true, 'isLocked' => false), array());
    if (is_null($user)) 
      return new Response("L'utilisateur demandé d'existe pas", Response::HTTP_NOT_FOUND);

    $isFriend = false;
    if (!$self) {
      $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    } else {
      $current = $user;
    }
    if (!$self) {
      $isFriend = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend')
        ->createQueryBuilder('l')
        ->where("(l.source = :current AND l.dest = :user) OR (l.source = :user AND l.dest = :current)")
        ->setParameters(array(
          'current' => $current->getId(),
          'user' => $user->getId(),
          )
        )
        ->getQuery()
        ->getResult();
    }

    $blank = 0;
    $pregnant = false;
    $icones = array();
    $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user);
    switch ($type->getType()->getName()) {
      case 'type-enceinte':
        $icones[] = 'enceinte';
        $pregnant = true;
        break;
      case 'type-pma':
      case 'type-prequenceinte':
        $icones[] = 'femme';
        $icones[] = 'bientot';
        break;
      case 'type-adoptante':
        $icones[] = 'femme';
        $icones[] = 'adoptante';
        break;
      case 'type-mamange':
        $icones[] = 'femme';
        $icones[] = 'mamange';
        break;
      default:
        $icones[] = 'femme';
        break;
    }
    if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) !== null) {
      if (is_object($woman->getMarriage())) {
        $title = $woman->getMarriage()->getDescFR();
        switch ($woman->getMarriage()->getIcon()) {
          case 'veuve':
            $icones[] = 'veuve';
            break;
          case 'couple':
            $icones[] = 'couple';
            break;
          case 'separee':
            $icones[] = 'separee';
            break;
          default:
            $blank++;
            break;
        }
      } else {
        $title = 'Femme';
        $blank++;
      }
      $woman = null; unset($woman);
    } else {
      $title = 'Femme';
      $blank++;
    }
    $family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user);

    if (is_object($family) && is_object($family->getSize())) {
      $title .= ' avec '.lcfirst($family->getSize()->getDescFR());
      //$icones[] = 'enfant';
      $count = 0;
      foreach($this->getDoctrine()->getRepository('MommyProfilBundle:Child')->findByUser($user) as $child) {
        if ($child->getSex()->getName() == 'fille') $icones[] = 'fille';
        else $icones[] = 'garcon';
        if ($count == 2) break;
      }
      if ($count ==0) $icones[] = 'enfant';
      else if ($count < 2) $blank++;
      if ($pregnant) $title .= " en attente d'un autre enfant";
    } else {
      if ($pregnant) $title .= ' en attente du premier';
      else $title .= ' sans enfant';
      $blank += 2;
    }
    $family = null; unset($family);

    for ($i=0; $i < $blank; $i++)
      $icones[] = 'blank';

    return array(
      'title' => $title,
      'icones' => $icones,
      );
  }

  /**
   * @Route("/albums/{uid}", name="profil-albums", defaults={"uid" = null})
   * @Template
   */
  public function albumsAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (is_null($uid)) {
      if (is_object($session->get('_user'))) {
        $uid = $session->get('_user')->getId();
        $self = true;
        $action = $this->generateUrl('profil-post', array());
      } else {
        return $this->forward('MommySecurityBundle:Default:loginForm', array());
      }
    } else {
      $self = false;
      $action = $this->generateUrl('profil-post', array())."/$uid";
    }
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneBy(array('id' => $uid, 'isActive' => true, 'isLocked' => false), array());

    return array();
  }

  /**
   * @Route("/voir/{uid}", name="profil-voir", defaults={"uid" = null})
   * @Template
   */
  public function voirAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (is_null($uid)) {
      if (is_object($session->get('_user'))) {
        $uid = $session->get('_user')->getId();
        $self = true;
        $action = $this->generateUrl('profil-post', array());
      } else {
        return $this->forward('MommySecurityBundle:Default:loginForm', array());
      }
    } else {
      $self = false;
      $action = $this->generateUrl('profil-post', array())."/$uid";
    }
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneBy(array('id' => $uid, 'isActive' => true, 'isLocked' => false), array());
    if (is_null($user)) 
      return new Response("L'utilisateur demandé d'existe pas", Response::HTTP_NOT_FOUND);

    $isFriend = false;
    if (!$self) {
      $current = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    } else {
      $current = $user;
    }
    if (!$self) {
      $isFriend = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend')
        ->createQueryBuilder('l')
        ->where("(l.source = :current AND l.dest = :user) OR (l.source = :user AND l.dest = :current)")
        ->setParameters(array(
          'current' => $current->getId(),
          'user' => $user->getId(),
          )
        )
        ->getQuery()
        ->getResult();
    }
    if (!$self && !$isFriend) {
      $full = false;
      //return new Response('Vous ne pouvez voir cet utilisateur', Response::HTTP_FORBIDDEN);
    } else {
      $full = true;
    }

    $city = $user->getCity();
    $pregnancy = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user);
    $birthday = time() - $user->getBirthday();

    $form = $this->createForm(new MessageForm($this->getDoctrine()->getManager(), $this->container));
    $forms = array(
      'hobbies' => $this->createForm(new HobbyForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      'movies' => $this->createForm(new MovieForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      'series' => $this->createForm(new SerieForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      'musics' => $this->createForm(new MusicForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      'vips' => $this->createForm(new VIPForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      'carousel' => $this->createForm(new CarouselForm($this->getDoctrine()->getManager(), $this->container))->createView(),
      );

    $messages = $this->getDoctrine()->getRepository('MommyProfilBundle:Message')
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
    $likes = array();
    $comments = array();
    $previews = array();
    foreach ($messages as $message) {
      $comments[$message->getId()] = $this->getDoctrine()->getRepository('MommyProfilBundle:Comment')->findBy(array('message' => $message), array('date' => 'ASC'));
      $likes[$message->getId()] = $this->getDoctrine()->getRepository('MommyProfilBundle:LikeMessage')
        ->createQueryBuilder('l')
        ->select('count(l.id)')
        ->where('l.message = :message')
        ->setParameters(array(
          'message' => $message->getId(),
          )
        )
        ->getQuery()
        ->getSingleScalarResult();
      if ($message->getLink() !== null) {
        $previews[$message->getId()] = $this->getDoctrine()->getRepository('MommyUIBundle:ExternalLink')->find($message->getLink());
      }
    }

    if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) !== null) {
      $activities = array();
      foreach ($woman->getActivities() as $a) {
        $activities[] = $this->getDoctrine()->getRepository('MommyProfilBundle:Activity')->findOneByName($a);
      }
      $movies = $woman->getMovies();
      $series = $woman->getTVShows();
      $musics = $woman->getMusics();
      $vips = $woman->getVIP();

      $em = $this->getDoctrine()->getManager();
      foreach ($movies as $movie) {
        if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findBy(array('desc_fr' => $movie), array())) === null) {
          $db = new Movie();
          $db->setName(sha1($movie));
          $db->setDescFR($movie);
          $em->persist($db);
          $em->flush();
          $em->clear();
        }
      }

      foreach ($musics as $music) {
        if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findBy(array('desc_fr' => $music), array())) === null) {
          $db = new Music();
          $db->setName(sha1($music));
          $db->setDescFR($music);
          $em->persist($db);
          $em->flush();
          $em->clear();
        }
      }

      foreach ($series as $show) {
        if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findBy(array('desc_fr' => $show), array())) === null) {
          $db = new TVShow();
          $db->setName(sha1($show));
          $db->setDescFR($show);
          $em->persist($db);
          $em->flush();
          $em->clear();
        }
      }

      foreach ($vips as $vip) {
        if (($db = $this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findBy(array('desc_fr' => $vip), array())) === null) {
          $db = new VIP();
          $db->setName(sha1($vip));
          $db->setDescFR($vip);
          $em->persist($db);
          $em->flush();
          $em->clear();
        }
      }
    } else {
      $activities = array();
      $movies = array();
      $series = array();
      $musics = array();
      $vips = array();
    }

    $carousel = $this->getDoctrine()->getRepository('MommyProfilBundle:Carousel')->findOneByUser($user);
    $manege = $this->getDoctrine()->getRepository('MommyProfilBundle:CarouselPhoto')->findByCarousel($carousel);

    return array(
      'profil' => $user,
      'city' => $city,
      'self' => $self,
      'pregnancy' => $pregnancy,
      'form' => $form->createView(),
      'messages' => $messages,
      'action' => $action,
      'forms' => $forms,
      'woman' => $woman,
      'activities' => $activities,
      'movies' => $movies,
      'series' => $series,
      'musics' => $musics,
      'vips' => $vips,
      'likes' => $likes,
      'comments' => $comments,
      'previews' => $previews,
      'manege' => $manege,
      'full' => $full,
      );
  }

  /**
   * @Route("/passion", name="profil-passion")
   */
  public function passionAction() {
    $choices = array(
      'hobbies' => array(),
      'movies' => array(),
      'series' => array(),
      'musics' => array(),
      'vips' => array(),
      );
    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:Activity')->findAll() as $choice) 
        $choices['hobbies'][] = array('id' => $choice->getName(), 'text' => $choice->getDescFR());
    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findAll() as $choice) 
        $choices['movies'][] = array('id' => $choice->getName(), 'text' => $choice->getDescFR());
    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findAll() as $choice) 
        $choices['musics'][] = array('id' => $choice->getName(), 'text' => $choice->getDescFR());
    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findAll() as $choice) 
        $choices['series'][] = array('id' => $choice->getName(), 'text' => $choice->getDescFR());
    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findAll() as $choice) 
        $choices['vips'][] = array('id' => $choice->getName(), 'text' => $choice->getDescFR());
    return new JsonResponse($choices);
  }

  /**
   * @Route("/vip/", name="profil-vip")
   * @Template
   */
  public function vipAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->find($session->get('_user')->getId());
    $vips = $woman->getVIP();
    $param = $request->query->get('vip');
    if (!is_array($param) || !isset($param['name']) || (trim($param['name']) == ''))
      return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);
    $em = $this->getDoctrine()->getManager();
    if (($vip = $this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findOneBy(array('name' => $param['name']))) === null) {
      if (($vip = $this->getDoctrine()->getRepository('MommyProfilBundle:VIP')->findOneBy(array('desc_fr' => $param['name']))) === null) {
        $vip = new VIP();
        $vip->setDescFR($param['name']);
        $vip->setName(md5($param['name']));
        $em->persist($vip);
        $em->flush();
        $em->clear();
      } 
    }
    if (in_array($vip->getName(), $vips))
      return new Response('Déjà présent', Response::HTTP_BAD_REQUEST);
    $vips[] = $vip->getName();
    $woman->setVIP(implode(',', $vips));
    $em->flush();
    $em->clear();
    return array('vip' => $vip->getDescFR());
  }

  /**
   * @Route("/hobby/", name="profil-hobby")
   * @Template
   */
  public function hobbyAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->find($session->get('_user')->getId())) === null)
      return array('hobby' => '');
    $activities = $woman->getActivities();
    $param = $request->query->get('hobby');
    if (!is_array($param) || !isset($param['name']) || (trim($param['name']) == ''))
      return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);
    $em = $this->getDoctrine()->getManager();
    if (($hobby = $this->getDoctrine()->getRepository('MommyProfilBundle:Activity')->findOneBy(array('name' => $param['name']))) === null) {
      if (($hobby = $this->getDoctrine()->getRepository('MommyProfilBundle:Activity')->findOneBy(array('desc_fr' => $param['name']))) === null) {
        $hobby = new Activity();
        $hobby->setDescFR($param['name']);
        $hobby->setName(md5($param['name']));
        $em->persist($hobby);
        $em->flush();
        $em->clear();
      } 
    }
    if (in_array($hobby->getName(), $activities))
      return new Response('Déjà présent', Response::HTTP_BAD_REQUEST);
    $activities[] = $hobby->getDescFR();
    $woman->setActivities(implode(',', $activities));
    $em->flush();
    $em->clear();
    return array('hobby' => $hobby->getDescFR());
  }

  /**
   * @Route("/movie/", name="profil-movie")
   * @Template
   */
  public function movieAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->find($session->get('_user')->getId())) === null) {
      return array('movie' => '');
    }
    $movies = $woman->getMovies();
    $param = $request->query->get('movie');
    if (!is_array($param) || !isset($param['name']) || (trim($param['name']) == ''))
      return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);
    $em = $this->getDoctrine()->getManager();
    if (($movie = $this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findOneBy(array('name' => $param['name']))) === null) {
      if (($movie = $this->getDoctrine()->getRepository('MommyProfilBundle:Movie')->findOneBy(array('desc_fr' => $param['name']))) === null) {
        $movie = new Movie();
        $movie->setDescFR($param['name']);
        $movie->setName(md5($param['name']));
        $em->persist($movie);
        $em->flush();
        $em->clear();
      } 
    }
    if (in_array($movie->getName(), $movies))
      return new Response('Déjà présent', Response::HTTP_BAD_REQUEST);
    $movies[] = $movie->getDescFR();
    $woman->setMovies(implode(',', $movies));
    $em->flush();
    $em->clear();
    return array('movie' => $movie->getDescFR());
  }

  /**
   * @Route("/serie/", name="profil-serie")
   * @Template
   */
  public function serieAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    $woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->find($session->get('_user')->getId());
    $series = $woman->getTVShows();
    $param = $request->query->get('serie');
    if (!is_array($param) || !isset($param['name']) || (trim($param['name']) == ''))
      return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);
    $em = $this->getDoctrine()->getManager();
    if (($serie = $this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findOneBy(array('name' => $param['name']))) === null) {
      if (($serie = $this->getDoctrine()->getRepository('MommyProfilBundle:TVShow')->findOneBy(array('desc_fr' => $param['name']))) === null) {
        $serie = new TVShow();
        $serie->setDescFR($param['name']);
        $serie->setName(md5($param['name']));
        $em->persist($serie);
        $em->flush();
        $em->clear();
      } 
    }
    if (in_array($serie->getName(), $series))
      return new Response('Déjà présent', Response::HTTP_BAD_REQUEST);
    $series[] = $serie->getDescFR();
    $woman->setTVShows(implode(',', $series));
    $em->flush();
    $em->clear();
    return array('serie' => $serie->getDescFR());
  }

  /**
   * @Route("/music/", name="profil-music")
   * @Template
   */
  public function musicAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();
    $woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->find($session->get('_user')->getId());
    $musics = $woman->getMusics();
    $param = $request->query->get('music');
    if (!is_array($param) || !isset($param['name']) || (trim($param['name']) == ''))
      return new Response('Ajout invalide', Response::HTTP_BAD_REQUEST);
    $em = $this->getDoctrine()->getManager();
    if (($music = $this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findOneBy(array('name' => $param['name']))) === null) {
      if (($music = $this->getDoctrine()->getRepository('MommyProfilBundle:Music')->findOneBy(array('desc_fr' => $param['name']))) === null) {
        $music = new Music();
        $music->setDescFR($param['name']);
        $music->setName(md5($param['name']));
        $em->persist($music);
        $em->flush();
        $em->clear();
      } 
    }
    if (in_array($music->getName(), $musics))
      return new Response('Déjà présent', Response::HTTP_BAD_REQUEST);
    $musics[] = $music->getDescFR();
    $woman->setMusics(implode(',', $musics));
    $em->flush();
    $em->clear();
    return array('music' => $music->getDescFR());
  }

  /**
   * @Route("/editer", name="profil-editer")
   * @Template
   */
  public function editerAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (!is_object($session->get('_user'))) {
      return new Response('Vous ne pouvez éditer cet utilisateur', Response::HTTP_FORBIDDEN);
    }
    $uid = $session->get('_user')->getId();
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    $form = $this->createForm(new ProfilForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (! $form->isValid()) {
        return new Response('Réponses invalides', Response::HTTP_BAD_REQUEST);
      }
      $update = $form->getData();
      if ($user->getFirstname() != $update['firstname'])
        $user->setFirstname($update['firstname']);
      if ($user->getLastname() != $update['lastname'])
        $user->setLastname($update['lastname']);
      if ($user->getEmail() != $update['email']) {
        $user->setEmail($update['email']);
        $user->setUsername($update['email']);
      }
      if ($user->getDescription() != $update['description'])
        $user->setDescription($update['description']);
      if (is_object($update['photo']) && $update['photo']->getClientSize()) {
        $url = sprintf(
          '%s%s',
          $this->container->getParameter('aws_base_url'),
          $this->getPhotoUploaderS3()->upload($update['photo'])
        );
        $user->setPhoto($url);
      }
      $em = $this->getDoctrine()->getManager();
      if (isset($_POST['profil']['address']) && !empty($_POST['profil']['address'])) {
        if (($address = $this->getDoctrine()->getRepository('MommyProfilBundle:Address')->findOneByLiteral($_POST['profil']['address'])) === null) {
          $address = new Address();
          $address->setLiteral($_POST['profil']['address']);
          $em->persist($address);
          $em->flush();
          $em->clear();
        }
        if (is_null($address))
          $address = $this->getDoctrine()->getRepository('MommyProfilBundle:Address')->findOneByLiteral($_POST['profil']['address']);
        $user->setAddress($address);
      }

      $em->flush();
      $em->clear();
    }
    return array(
      'profil' => $user, 
      'form' => $form->createView(),
    );
  }

  /**
   * @Route("/amis/ajouter/{uid}", name="profil-add-friend", requirements={"uid"=".+"})
   * @Template
   */
  public function addFriendAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $friend = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    if (($this->getDoctrine()->getRepository('MommyProfilBundle:Friend')->findOneBy(array('source' => $user, 'dest' => $friend), array())) === null) {
      if (($this->getDoctrine()->getRepository('MommyProfilBundle:Friend')->findOneBy(array('dest' => $user, 'source' => $friend), array())) === null) {
        $demand = new Friend();
        $demand->setSource($user);
        $demand->setDest($friend);
        $demand->setEnabled(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($demand);
        $em->flush();
        $em->clear();
      }
    }
    return new JsonResponse(array('success' => true));
  }

  /**
   * @Route("/amis/accepter/{uid}", name="profil-accept-friend", requirements={"uid"=".+"})
   * @Template
   */
  public function acceptFriendAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($friend = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend')->findOneBy(array('dest' => $user, 'id' => $uid), array())) !== null) {
        $friend->setEnabled(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($friend);
        $em->flush();
        $em->clear();
    }

    return new JsonResponse(array('success' => true));
  }

  /**
   * @Route("/amis/refuser/{uid}", name="profil-deny-friend", requirements={"uid"=".+"})
   * @Template
   */
  public function denyFriendAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    if (($friend = $this->getDoctrine()->getRepository('MommyProfilBundle:Friend')->findOneBy(array('dest' => $user, 'id' => $uid), array())) !== null) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($friend);
        $em->flush();
        $em->clear();
    }

    return new JsonResponse(array('success' => true));
  }

  /**
   * @Route("/reseau/ajouter/{uid}", name="profil-add-network", requirements={"uid"=".+"})
   * @Template
   */
  public function addNetworkAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId());
    $friend = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    if (($this->getDoctrine()->getRepository('MommyProfilBundle:Network')->findOneBy(array('source' => $user, 'dest' => $friend), array())) === null) {
      if (($this->getDoctrine()->getRepository('MommyProfilBundle:Network')->findOneBy(array('dest' => $user, 'source' => $friend), array())) === null) {
        $demand = new Network();
        $demand->setSource($user);
        $demand->setDest($friend);

        $em = $this->getDoctrine()->getManager();
        $em->persist($demand);
        $em->flush();
        $em->clear();
      }
    }

    return new JsonResponse(array('success' => true));
  }

  /**
   * @Route("/manege", name="profil-manege")
   * @Template
   */
  public function manegeAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    if (!is_object($session->get('_user'))) {
      return new Response('Vous ne pouvez éditer cet utilisateur', Response::HTTP_FORBIDDEN);
    }
    $uid = $session->get('_user')->getId();
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    $form = $this->createForm(new CarouselForm($this->getDoctrine()->getManager(), $this->container));
    if ($request->getMethod() == 'POST') {
      $form->handleRequest($request);
      if (! $form->isValid()) {
        return new Response('Réponses invalides', Response::HTTP_BAD_REQUEST);
      }
      $update = $form->getData();
      if (is_object($update['file']) && $update['file']->getClientSize()) {
        $url = sprintf(
          '%s%s',
          $this->container->getParameter('aws_base_url'),
          $this->getPhotoUploaderS3()->upload($update['file'])
        );
        $em = $this->getDoctrine()->getManager();

        if (($manege = $this->getDoctrine()->getRepository('MommyProfilBundle:Carousel')->findOneByUser($user)) === null) {
          $manege = new Carousel();
          $manege->setUser($user);
          $em->persist($manege);
          $em->flush();
          $em->clear();
        }
        $manege = $this->getDoctrine()->getRepository('MommyProfilBundle:Carousel')->findOneByUser($user);

        $photo = new CarouselPhoto();
        $photo->setCarousel($manege);
        $photo->setUrl($url);
        $em->persist($photo);
        $em->flush();
        $em->clear();
      }
    }
    return $this->forward('MommyProfilBundle:Default:voir', array('uid' => null));
  }

  protected function getPhotoUploaderS3() {
    return $this->get('amazonS3.photo_uploader');
  }

  protected function getPhotoUploaderHelios() {
    return $this->get('helios.photo_uploader');
  }

  /**
   * @Route("/albums", name="profil-albums")
   * @Template
   */
  public function albumSumAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    return array();
  }

  /**
   * @Route("/questions.json", name="profil-questions-json")
   */
  public function questionsAction() {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $questions = array();

    $uid = $session->get('_user')->getId();
    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid);

    $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user);
    $questions['type'] = str_replace('type-', '', $type->getType()->getName());
    /*
  "type-enceinte"
  "type-famille"
  "type-pma"
  "type-presquenceinte"
  "type-adoptante"
  "type-mamange"
  "type-pro"
  "type-rien"
    */
  }
}