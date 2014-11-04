<?php

namespace Mommy\LegalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mommy_legal")
     * @Template
     */
    public function indexAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        return array();
    }

    /**
     * @Route("/html", name="legal-html")
     * @Template
     */
    public function htmlAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        return array();
    }

    /**
     * @Route("/display.json", name="legal-display-json")
     */
    public function displayAction() {
        MommyUIBundle::logStatistics($this->get('request'));
        $display = array(
            "0" => array(
                'frame' => '#center',
                'name' => 'legal',
                'html' => '/mentions-legales/html',
                'title' => 'Mentions légales',
                'empty' => true,
            ),
        );
        return new JsonResponse($display);
    }
}