<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $serializer = $this->get('serializer');
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => $serializer->serialize($this->container->getParameter('kernel.root_dir'), 'json', array('groups' => array('group1'))),
        ));
    }
}
