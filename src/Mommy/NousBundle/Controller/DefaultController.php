<?php

namespace Mommy\NousBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mommy_nous")
     * @Template
     */
    public function indexAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        return array();
    }

    /**
     * @Route("/html", name="nous-html")
     * @Template
     */
    public function htmlAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        return array();
    }

    /**
     * @Route("/display.json", name="nous-display-json")
     */
    public function displayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'nous',
                'html' => '/qui-sommes-nous/html',
                'title' => 'Qui sommes nous ?',
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }
}