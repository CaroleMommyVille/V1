<?php

namespace Mommy\QuestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

// Question
use Mommy\QuestionBundle\Entity\Question;
// Answer
use Mommy\QuestionBundle\Entity\Answer;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/questions.json")
     * @Template
     */
    public function questionsAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $securityContext = $this->container->get('security.context');
	    if( $securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') ){
          return new JsonResponse(array(
            'question' => null,
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
          		$collect = $this->getDoctrine()->getRepository('Question')->findBy(array('active' => true));
          		$questions = array();
          		foreach ($collect as $question)
            		$questions[] = $question->getId();
          		return new JsonResponse(array(
            		'questions' => $questions,
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
     * @Route("/question.json")
     * @Template
     */
    public function questionAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $securityContext = $this->container->get('security.context');
	    if( $securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') ){
          return new JsonResponse(array(
            'question' => null,
            'error' => 'ERR_NOT_CONNECTED',
          ));
	    } else {
	      $user = $this->get('security.context')->getToken()->getUser();
	      $qid = $request->request->get('qid');
	      $roles = $this->get('security.context')->getToken()->getUser()->getRoles();
	    }

 	    switch ($request->getMethod()) {
	    	case 'POST':
	        	// update	    
		        if (($qid < 0) || empty($qid))
		          	return new JsonResponse(array(
		            	'question' => null,
		            	'error' => 'ERR_INVALID_QUESTION',
		          	));
		        // get Question from parameters
		        $param = array(
		            'question'  => $request->request->get('question'),
		        );
	
			    $question = $this->getDoctrine()->getRepository('Question')->findBy(array('id' => $qid, 'active' => true));

		        if (($question->getOwner() == $user->getId()) || in_array('ROLE_ADMIN', $roles)) {
		          	foreach ($param as $p) {
		            	$set = 'set'.ucfirst($p);
		            	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
		            	$question->$set($param['p']);
		          	}

		        	$em = $this->getDoctrine()->getManager();
		        	$em->persist($question);
		         	$em->flush();
		          
		          	return new JsonResponse(array(
		            	'question' => array(
		            		'owner' => $question->getOwner(),
			            	'question' => $question->getQuestion(),
			            	'submitted' => $question->getSubmitted(),
			            	'active' => $question->getActive(),
		    	        	'answers' => null,
		        	    ),
		            	'error' => 'ERR_OK',
		        	));
		        }
		        return new JsonResponse(array(
		          'question' => null,
		          'error' => 'ERR_INVALID_REQUEST',
		        ));		    
	     	case 'PUT':
	        	// create	    
		        // get Question from parameters
		        $param = array(
		            'question'  => $request->request->get('question'),
		           	'submitted' => date('YmdHms', time()),
		        );

			    $question = new Question();

		        foreach ($param as $p) {
		           	$set = 'set'.ucfirst($p);
		           	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
		           	$question->$set($param['p']);
		      	}
	        	$question->setActive(true);
	        	$question->setOwner($user->getId());

		        $em = $this->getDoctrine()->getManager();
		        $em->persist($question);
		        $em->flush();
		          
		        return new JsonResponse(array(
			        'question' => array(
			           	'owner' => $question->getOwner(),
			           	'question' => $question->getQuestion(),
			           	'submitted' => $question->getSubmitted(),
			           	'active' => $question->getActive(),
		            	'answers' => null,
		            ),
			        'error' => 'ERR_OK',
			    ));
				return new JsonResponse(array(
			    	'question' => null,
			        'error' => 'ERR_INVALID_REQUEST',
			    ));		    
			case 'GET':
		        // show	  
			    if (($qid < 0) || empty($qid))
			    	return new JsonResponse(array(
			        	'question' => null,
			            'error' => 'ERR_INVALID_QUESTION',
			        ));
			    $question = $this->getDoctrine()->getRepository('Question')->find($qid);
			    if ($question)
					return new JsonResponse(array(
				    	'question' => array(
				        	'owner' => $question->getOwner(),
				            'question' => $question->getQuestion(),
				            'submitted' => $question->getSubmitted(),
				            'active' => $question->getActive(),
				            'answers' => $question->getAnswers(),
				        ),
				        'error' => 'ERR_OK',
				    ));
				return new JsonResponse(array(
			    	'question' => null,
			        'error' => 'ERR_INVALID_REQUEST',
			    ));		    
		    case 'DELETE':
		        // delete
		        if (($qid < 0) || empty($qid))
		          return new JsonResponse(array(
		            'question' => null,
		            'error' => 'ERR_INVALID_QUESTION',
		          ));
		        // get Question from parameters
			    $question = $this->getDoctrine()->getRepository('Question')->find($qid);

		        if (($question->getOwner() == $user->getId()) || in_array('ROLE_ADMIN', $roles)) {
		        	$question->setActive(false);

		        	$em = $this->getDoctrine()->getManager();
		        	$em->persist($question);
		         	$em->flush();
		          
		          	return new JsonResponse(array(
		            	'question' => array(
		            		'owner' => $question->getOwner(),
			            	'question' => $question->getQuestion(),
			            	'submitted' => $question->getSubmitted(),
			            	'active' => $question->getActive(),
		    	        	'answers' => null,
		        	    ),
		            	'error' => 'ERR_OK',
		        	));
		        }
		        return new JsonResponse(array(
		          'question' => null,
		          'error' => 'ERR_INVALID_REQUEST',
		        ));		    
		    default:
		        throw new Exception("Error Processing Request", 1);
		}
   }

    /**
     * @Route("/answer.json")
     * @Template
     */
    public function answerAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    $securityContext = $this->container->get('security.context');
	    if( $securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') ){
          return new JsonResponse(array(
            'question' => null,
            'error' => 'ERR_NOT_CONNECTED',
          ));
	    } else {
	      $user = $this->get('security.context')->getToken()->getUser();
	      $qid = $request->request->get('qid');
	      $roles = $this->get('security.context')->getToken()->getUser()->getRoles();
	    }

 	    switch ($request->getMethod()) {
	    	case 'POST':
	        	// update	    
		        if (($aid < 0) || empty($aid))
			        return new JsonResponse(array(
		            	'answer' => null,
		            	'error' => 'ERR_INVALID_QUESTION',
		          	));
		        // get Question from parameters
		        $param = array(
		            'answer'  => $request->request->get('answer'),
		        );
	
			    $answer = $this->getDoctrine()->getRepository('Answer')->findBy(array('id' => $aid, 'active' => true));

		        if (($answer->getOwner() == $user->getId()) || in_array('ROLE_ADMIN', $roles)) {
		          	foreach ($param as $p) {
		            	$set = 'set'.ucfirst($p);
		            	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
		            	$answer->$set($param['p']);
		          	}

		        	$em = $this->getDoctrine()->getManager();
		        	$em->persist($answer);
		         	$em->flush();
		          
		          	return new JsonResponse(array(
		            	'answer' => array(
		            		'owner' => $answer->getOwner(),
		            		'question' => $answer->getQuestion(),
			            	'answer' => $answer->getAnswer(),
			            	'submitted' => $answer->getSubmitted(),
			            	'active' => $answer->getActive(),
		        	    ),
		            	'error' => 'ERR_OK',
		        	));
		        }
		        return new JsonResponse(array(
		          'answer' => null,
		          'error' => 'ERR_INVALID_REQUEST',
		        ));		    
	     	case 'PUT':
	        	// create	    
		        // get Question from parameters
		        $param = array(
		        	'question'  => $request->request->get('question'),
		            'answer'  => $request->request->get('answer'),
		           	'submitted' => date('YmdHms', time()),
		        );

			    $answer = new Answer();

		        foreach ($param as $p) {
		           	$set = 'set'.ucfirst($p);
		           	$set = preg_replace('/_[a-z]/e', strtoupper("$0"), $set);
		           	$answer->$set($param['p']);
		      	}
	        	$answer->setActive(true);
	        	$answer->setOwner($user->getId());

		        $em = $this->getDoctrine()->getManager();
		        $em->persist($answer);
		        $em->flush();
		          
		        return new JsonResponse(array(
			        'answer' => array(
			           	'owner' => $answer->getOwner(),
			           	'question' => $answer->getQuestion(),
			            'answer' => $answer->getAnswer(),
			           	'submitted' => $answer->getSubmitted(),
			           	'active' => $answer->getActive(),
		            ),
			        'error' => 'ERR_OK',
			    ));
				return new JsonResponse(array(
			    	'answer' => null,
			        'error' => 'ERR_INVALID_REQUEST',
			    ));		    
			case 'GET':
		        // show	  
			    if (($aid < 0) || empty($aid))
			    	return new JsonResponse(array(
			        	'answer' => null,
			            'error' => 'ERR_INVALID_QUESTION',
			        ));
			    $answer = $this->getDoctrine()->getRepository('Answer')->find($aid);
			    if ($answer)
					return new JsonResponse(array(
				    	'answer' => array(
				        	'owner' => $answer->getOwner(),
				            'question' => $answer->getQuestion(),
				            'answer' => $answer->getAnswer(),
				            'submitted' => $answer->getSubmitted(),
				            'active' => $answer->getActive(),
				        ),
				        'error' => 'ERR_OK',
				    ));
				return new JsonResponse(array(
			    	'answer' => null,
			        'error' => 'ERR_INVALID_REQUEST',
			    ));		    
		    case 'DELETE':
		        // delete
		        if (($aid < 0) || empty($aid))
		          return new JsonResponse(array(
		            'answer' => null,
		            'error' => 'ERR_INVALID_QUESTION',
		          ));
		        // get Question from parameters
			    $answer = $this->getDoctrine()->getRepository('Answer')->find($aid);

		        if (($answer->getOwner() == $user->getId()) || in_array('ROLE_ADMIN', $roles)) {
		        	$answer->setActive(false);

		        	$em = $this->getDoctrine()->getManager();
		        	$em->persist($answer);
		         	$em->flush();
		          
		          	return new JsonResponse(array(
		            	'answer' => array(
		            		'owner' => $answer->getOwner(),
			            	'question' => $answer->getQuestion(),
				            'answer' => $answer->getAnswer(),
			            	'submitted' => $answer->getSubmitted(),
			            	'active' => $answer->getActive(),
		        	    ),
		            	'error' => 'ERR_OK',
		        	));
		        }
		        return new JsonResponse(array(
		          'answer' => null,
		          'error' => 'ERR_INVALID_REQUEST',
		        ));		    
		    default:
		        throw new Exception("Error Processing Request", 1);
		}
	}

    /**
     * @Route("/dernieres")
     * @Template
     */
    public function lastAction()
    {
    	MommyUIBundle::logStatistics($this->get('request'));
	    $request = $this->get('request');
	    $session = $request->getSession();

	    return array();
	}
}