<?php

namespace Mommy\PubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

// Ad
use Mommy\QuestionBundle\Entity\Ad;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/ads.json")
     * @Template
     */
    public function adsAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $securityContext = $this->container->get('security.context');
	    if( $securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') ){
          return new JsonResponse(array(
            'ads' => null,
            'error' => 'ERR_NOT_CONNECTED',
          ));
	    } else {
	      $user = $this->get('security.context')->getToken()->getUser();
	      $uid = $request->request->get('uid');
	      $roles = $this->get('security.context')->getToken()->getUser()->getRoles();
	    }
	    switch ($request->getMethod()) {
		    case 'GET':
		        // show	    
          		$collect = $this->getDoctrine()->getRepository('Ad')->findBy(array('active' => true));
          		$ads = array();
          		foreach ($collect as $ad) {
          			if ($ad->getExpire() < date("YmdHis", time())) continue;
          			if ($ad->getSubmitted() > date("YmdHis", time())) continue;
            		$ads[] = $ad->getId();
            	}
          		return new JsonResponse(array(
            		'ads' => $ads,
            		'error' => 'ERR_OK',
          		));
	    	case 'POST':
	        	// update	    
	     	case 'PUT':
	        	// create	    
		    case 'DELETE':
		        // delete
		    	// no need here
		    default:
		        throw new Exception("Error Processing Request", 1);
		}        
    }

    /**
     * @Route("/ad.json")
     * @Template
     */
    public function adAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $securityContext = $this->container->get('security.context');
	    if( $securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') ){
          return new JsonResponse(array(
            'ads' => null,
            'error' => 'ERR_NOT_CONNECTED',
          ));
	    } else {
	      $user = $this->get('security.context')->getToken()->getUser();
	      $uid = $request->request->get('uid');
	      $roles = $this->get('security.context')->getToken()->getUser()->getRoles();
	    }
	    switch ($request->getMethod()) {
		    case 'GET':
		        // show	    
		        if (($aid < 0) || empty($aid))
		          	return new JsonResponse(array(
		            	'ad' => null,
		            	'error' => 'ERR_INVALID_AD',
		          	));
          		$ad = $this->getDoctrine()->getRepository('Ad')->find($aid);
          		if ($ad) {
	          		return new JsonResponse(array(
	            		'ads' => array(
	            			'owner' => $ad->getOwner(),
	            			'name' => $ad->getName(),
	            			'published' => $ad->getPublished(),
	            			'expire' => $ad->getExpire(),
	            			'code' => $ad->getCode(),
	            			'active' => $ad->getActive(),
	            			'url' => $ad->getUrl(),
	            			'picture' => $ad->getPicture(),
	            		),
	            		'error' => 'ERR_OK',
	          		));
	          	}
	          	return new JsonResponse(array(
	            	'ad' => null,
	            	'error' => 'ERR_INVALID_AD',
	          	));
	    	case 'POST':
	        	// update	    
		        if (($aid < 0) || empty($aid))
		          	return new JsonResponse(array(
		            	'ad' => null,
		            	'error' => 'ERR_INVALID_AD',
		          	));
		        $ad = null;
		        if (in_array('ROLE_ADMIN', $roles))
          			$ad = $this->getDoctrine()->getRepository('Ad')->find($aid);
          		else
          			$ad = $this->getDoctrine()->getRepository('Ad')->findBy(array('id' => $aid, 'owner' => $user->getId()));
          		if ($ad) {
          			$param = array(
			        	'name'  => $request->request->get('name'),
			            'published'  => $request->request->get('published'),
			            'expire'  => $request->request->get('expire'),
			            'code'  => $request->request->get('code'),
			            'active'  => $request->request->get('active'),
			            'url'  => $request->request->get('url'),
			            'picture'  => $request->request->get('picture'),
          			);
					foreach ($param as $p) {
			           	$set = 'set'.ucfirst($p);
			           	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
			           	$ad->$set($param['p']);
			      	}

			        $em = $this->getDoctrine()->getManager();
			        $em->persist($ad);
			        $em->flush();
          		}
	          	return new JsonResponse(array(
	            	'ad' => null,
	            	'error' => 'ERR_INVALID_AD',
	          	));
	     	case 'PUT':
	        	// create	    
          		$ad = new Ad();
          		if ($ad) {
          			$param = array(
          				'owner' => $user->getId(),
			        	'name' => $request->request->get('name'),
			            'published'  => $request->request->get('published'),
			            'expire'  => $request->request->get('expire'),
			            'code'  => $request->request->get('code'),
			            'active'  => $request->request->get('active'),
			            'url'  => $request->request->get('url'),
			            'picture'  => $request->request->get('picture'),
          			);
					foreach ($param as $p) {
			           	$set = 'set'.ucfirst($p);
			           	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
			           	$ad->$set($param['p']);
			      	}

			        $em = $this->getDoctrine()->getManager();
			        $em->persist($ad);
			        $em->flush();
          		}
	          	return new JsonResponse(array(
	            	'ad' => null,
	            	'error' => 'ERR_INVALID_AD',
	          	));
		    case 'DELETE':
		        // delete
		        if (($aid < 0) || empty($aid))
		          	return new JsonResponse(array(
		            	'ad' => null,
		            	'error' => 'ERR_INVALID_AD',
		          	));
		        $ad = null;
		        if (in_array('ROLE_ADMIN', $roles))
          			$ad = $this->getDoctrine()->getRepository('Ad')->find($aid);
          		else
          			$ad = $this->getDoctrine()->getRepository('Ad')->findBy(array('id' => $aid, 'owner' => $user->getId()));
          		if ($ad) {
          			$ad->setActive(false);

			        $em = $this->getDoctrine()->getManager();
			        $em->persist($ad);
			        $em->flush();
          		}
	          	return new JsonResponse(array(
	            	'ad' => null,
	            	'error' => 'ERR_INVALID_AD',
	          	));
		    default:
		        throw new Exception("Error Processing Request", 1);
		}        
    }
}