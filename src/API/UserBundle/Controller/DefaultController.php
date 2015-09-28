<?php

namespace API\UserBundle\Controller;


use FOS\RestBundle\Context\Context;
//use FOS\RestBundle\Controller\FOSRestController;
use API\UserBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class DefaultController
{

    public function indexAction()
    {
        $view = View::create();

        $view->setData(array('date' => date('r'), 'name' => 'some name'));
        $view->setTemplate('APIUserBundle:Default:index.html.twig')
            ->setTemplateData(array('name' => 'some name'));

        return $view;
    }

//    public function allAction()
//    {
//
//        $em = $this->getDoctrine()->getManager();
//
//        $users = $em->getRepository('APIUserBundle:User')->findAll();
//
//        $view = $this->view($users, 200)
//            ->setTemplate("APIUserBundle:Default:getUsers.html.twig")
//            ->setTemplateVar('users')
//        ;
//
//        return $this->handleView($view);
//
//    }
//
//    public function getAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $user = $em->getRepository('APIUserBundle:User')->findPk($id);
//
//        if (!$user instanceof User) {
//            throw new NotFoundHttpException('User not found');
//        }
//
//        return array('user' => $user);
//    }
}
