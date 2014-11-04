<?php

namespace Mommy\CharteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mommy_charte")
     * @Template
     */
    public function indexAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        return array();
    }

    /**
     * @Route("/html", name="charte-html")
     * @Template
     */
    public function htmlAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        return array();
    }

    /**
     * @Route("/display.json", name="charte-display-json")
     */
    public function displayAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'charte',
                'html' => '/charte/html',
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }
}