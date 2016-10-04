<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParamValue;
use AppBundle\Entity\User;
use AppBundle\Stuff\UserStuff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()  
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute("user_signin");
        }

        $user = $this->getUser();
        $user_roles = $user->getUserGroup();

        return $this->redirect($user_roles->getUrl());
    }

    /**
     * @param User $user
     * @return Response
     *
     * @Route("/user/{ID_User}", name="user_info")
     */
    public function userInfoAction(User $user)
    {
        /** @var $user_stuff UserStuff */
        $user_stuff = $this->get("app.user_stuff");

        $user_params = $user_stuff->getUserParamList($user);

        return $this->render("default/view_user.html.twig", array(
            "user" => $user,
            "user_params" => $user_params,
        ));
    }
}
