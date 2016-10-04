<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/signin", name="user_signin")
     */
    public function loginAction(Request $request)
    {
        $authUtils = $this->get("security.authentication_utils");
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute("homepage");
        }

        //Getting last error if there was one
        $lastError = $authUtils->getLastAuthenticationError();

        $lastUserName = $authUtils->getLastUserName();

        return $this->render(
          "security/login.html.twig",
            array(
                "last_username" => $lastUserName,
                "last_error" => $lastError
            )
        );
    }

    /**
     * @Route("/signup", name="user_signup")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $userGroup = $this->getDoctrine()->getRepository("AppBundle:UserGroup")->find(5);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $password = $this->get("security.password_encoder")
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setUserGroup($userGroup);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("user_signin");
        }

        return $this->render(
            "security\\register.html.twig",
            array(
                "form" => $form->createView()
            )
        );
    }

    /**
     * @Route("/recover", name="password_recover")
     */
    public function recoverAction(Request $request)
    {
        return $this->redirectToRoute("coming_soon");
    }
}