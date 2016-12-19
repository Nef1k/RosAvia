<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParamValue;
use AppBundle\Entity\User;
use AppBundle\Stuff\UserStuff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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
    public function userInfoAction(Request $request, User $user)
    {
        /** @var $user_stuff UserStuff */
        $user_stuff = $this->get("app.user_stuff");
        /** @var  $user_auth User*/
        $user_auth = $this->getUser();
        if (!(($user_auth) && ((in_array('ROLE_ADMIN',$user_auth->getRoles())) || ((in_array('ROLE_DEALER',$user_auth->getRoles())) && ($user->getIDUser() == $user_auth->getIDUser()))))) {
            return $this->redirectToRoute("user_signin");
        }
        $user_params = $user_stuff->getUserParamList($user);

        $template_params = [
            "user" => $user,
            "user_params" => $user_params,
            "auth_user_group" => $user_auth->getUserGroup()->getIDUserGroup()
        ];

        $file_code = $request->query->get("file_code", -1);
        if ($file_code != -1)
        {
            $file_msg = $this->get("app.file_stuff")->getFileMsg($file_code);
            $template_params["file_msg"] = $file_msg;
            $template_params["file_msg_code"] = $file_code;
        }

        return $this->render("default/view_user.html.twig", $template_params);
    }

}
