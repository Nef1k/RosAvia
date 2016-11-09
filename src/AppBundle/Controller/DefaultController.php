<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParamValue;
use AppBundle\Entity\User;
use AppBundle\Stuff\UserStuff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
        /** @var  $user_auth User*/
        $user_auth = $this->getUser();
        if (!(($user_auth) && ((in_array('ROLE_ADMIN',$user_auth->getRoles())) || ((in_array('ROLE_DEALER',$user_auth->getRoles())) && ($user->getIDUser() == $user_auth->getIDUser()))))) {
            return $this->redirectToRoute("user_signin");
        }
        $user_params = $user_stuff->getUserParamList($user);
        return $this->render("default/view_user.html.twig", array(
            "user" => $user,
            "user_params" => $user_params,
            "auth_user_group" => $user_auth->getUserGroup()->getIDUserGroup(),
            "file_msg_code" => 0,
            "file_msg" => ""
        ));
    }
}
