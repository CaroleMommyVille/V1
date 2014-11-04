<?php

namespace Mommy\SondageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mommy\SondageBundle\Form\SondageForm;

class DefaultController extends Controller
{
    /**
     * @Route("/derniers")
     * @Template()
     */
    public function lastAction()
    {
    	$form = $this->createForm(new SondageForm($this->getDoctrine()->getManager(), $this->container));
    	return array(
     		'form' => $form->createView(),
        );
    }
}
