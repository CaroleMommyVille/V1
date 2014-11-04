<?php

namespace Mommy\CGUBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mommy_cgu")
     * @Template
     */
    public function indexAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        return array();
    }

    /**
     * @Route("/html", name="cgu-html")
     * @Template
     */
    public function htmlAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        return array();
    }

    /**
     * @Route("/display.json", name="cgu-display-json")
     */
    public function displayAction() {
        $request = $this->get('request');
        MommyUIBundle::logStatistics($request);
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'cgu',
                'html' => '/cgu/html',
                'title' => "Conditions générales d'utilisation",
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }
}