<?php

namespace Mommy\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request)
    {
    	MommyUIBundle::logStatistics($request);
        return array();
    }
}
