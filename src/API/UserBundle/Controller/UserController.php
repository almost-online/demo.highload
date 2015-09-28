<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Entity\User;
//use API\USerBundle\Model\UserQuery;
use API\UserBundle\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Rest\View
     */
    public function allAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('APIUserBundle:User')->findAll();

        return array('users' => $users);
    }

    /**
     * @Rest\View
     */
    public function getAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('APIUserBundle:User')->findPk($id);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        return array('user' => $user);
    }

    public function newAction()
    {
        return $this->_processForm(new User());
    }

    private function _processForm(User $user)
    {
        $statusCode = $user->isNew() ? 201 : 204;

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            /** @var ObjectManager $em */
            $em = $this->getDoctrine()->getManager();

            /** @var PasswordEncoderInterface $encoder */
            $encoder = $factory->getEncoder($user);

            $data = $form->getData();
            try {
                $user->createNewAccount($encoder, $em, $data->getEmail(), $data->getPassword());

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_user_get', array('id' => $user->getId()),
                            true // absolute
                        )
                    );
                }

                return $response;
            } catch (\PDOException $e) {
                $form->addError(new FormError("Same user already exists!"));
            }
        }

        return View::create($form, 400);
    }
}
