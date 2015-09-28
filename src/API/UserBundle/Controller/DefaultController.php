<?php

namespace API\UserBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;


class DefaultController extends FOSRestController
{

    public function indexAction()
    {
        $data = array('date' => date('r'));

        $view = $this->view($data, 200);
        $view->setTemplate('APIUserBundle:Default:index.html.twig')
            ->setTemplateData(array('name' => 'some name'));

        return $this->handleView($view);
    }
}
