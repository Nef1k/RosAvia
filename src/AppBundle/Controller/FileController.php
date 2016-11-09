<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 13.10.2016
 * Time: 22:37
 */

namespace AppBundle\Controller;

use AppBundle\DataClasses\UserIDCheck;
use AppBundle\Entity\FileCategory;
use AppBundle\Entity\File;
use AppBundle\Entity\User;
use AppBundle\Stuff\FileStuff;
use AppBundle\Stuff\UserStuff;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;


class FileController extends Controller
{

    /**
     * @param Request $request
     * @Method("POST")
     * @Route("/files/select", name="file_select")
     * @return Response
     */
    public function getUserFilesAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.file_stuff");
        $response = new Response();
        $response->setContent(json_encode($file_stuff->GetFileArrayFromRequest($request)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/files/file_get/{ID_User}/{ID_File}", name="file_get")
     * @return Response
     */
    public function getUserFileAction(User $user, File $file){
        /** @var $user User */
        /** @var $file File */
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.file_stuff");

        $file_code = $file_stuff->GetFileFromRequest($user->getIDUser(), $file->getIDFile());
        return $this->redirectToRoute('user_info', [
            "ID_User" => $user->getIDUser(),
            "file_code" => $file_code,
        ]);

        /*
        if (count($Request_output['error_msg']) != 0) {
            return $this->render("default/view_user.html.twig", array(
                "user" => $user,
                "user_params" => $user_params,
                "auth_user_group" => $this->getUser()->getUserGroup()->getIDUserGroup(),
                "file_msg_code" => 2,
                "file_msg" => $Request_output['error_msg'][0]
            ));
        } else {
            return $this->render("default/view_user.html.twig", array(
                "user" => $user,
                "user_params" => $user_params,
                "auth_user_group" => $this->getUser()->getUserGroup()->getIDUserGroup(),
                "file_msg_code" => 1,
                "file_msg" => "Загрузка файла завершена успешно!"
            ));
        }*/
    }

    /**
     * @param Request $request
     * @Method("POST")
     * @Route("/files/file_set", name="file_set")
     * @return Response
     */
    public function setUserFileAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.file_stuff");
        $user_id = new UserIDCheck();
        $user_id->setUserID($request->request->get("user_id"));
        $display_name = $request->request->get("display_name");
        $validator = $this->get('validator');
        $errors = $validator->validate($user_id);
        $em = $this->getDoctrine()->getManager();
        /** @var  $user User*/
        $user = $em->getRepository("AppBundle:User")->find($user_id->getUserID());
        $file_cat_name = substr(basename($_FILES['userfile']['name']), (stripos($_FILES['userfile']['name'],'.') !== false)?stripos($_FILES['userfile']['name'],'.') + 1:strlen(basename($_FILES['userfile']['name'])));
        /** @var  $file_cat FileCategory*/
        $date = new \DateTime();
        if ($file_stuff->PushFile($user->getIDUser(), $file_cat_name, $display_name, $date)){
            $file_msg_code = 1;
            $file_msg = "Файл успешно загружен!";
        } else {
            $file_msg_code = 8;
            $file_msg = "Внимание! Загрузка файла не была завершена успешна!";
        }

        return $this->redirectToRoute("user_info", [
            "ID_User" => $user_id->getUserID(),
            "file_code" => $file_msg_code
        ]);
        /*return $this->render("default/view_user.html.twig", array(
            "user" => $user,
            "user_params" => $user_params,
            "auth_user_group" => $this->getUser()->getUserGroup()->getIDUserGroup(),
            "file_msg_code" => $file_msg_code,
            "file_msg" => $file_msg));*/
    }
}