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
use AppBundle\Stuff\FileStuff;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;


class FileController extends Controller
{

    /**
     * @param Request $request
     * Method("GET")
     * Route("/files/select", name="file_select")
     * @return Response
     */
    public function getUserFilesAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.certificate_stuff");
        $response = new Response();
        $response->setContent(json_encode($file_stuff->GetFileArrayFromRequest($request)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * Method("GET")
     * Route("/files/file_get", name="file_get")
     * @return Response
     */
    public function getUserFileAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.certificate_stuff");
        $response = new Response();
        $response->setContent(json_encode($file_stuff->GetFileFromRequest($request)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * Method("GET")
     * Route("/files/file_set", name="file_get")
     */
    public function setUserFileAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.certificate_stuff");
        $user_id = new UserIDCheck();
        $user_id->setUserID($request->request->get("user_id"));
        $display_name = $request->request->get("display_name");
        $validator = $this->get('validator');
        $errors = $validator->validate($user_id);
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        $file_cat_name = substr(basename($_FILES['userfile']['name']), (stripos($_FILES['userfile']['name'],'.') !== false)?stripos($_FILES['userfile']['name'],'.'):strlen(basename($_FILES['userfile']['name'])));
        /** @var  $file_cat FileCategory*/
        $response = new Response();
        $date = new \DateTime();
        $response->setContent(json_encode($file_stuff->PushFile($user_id->getUserID(),$file_cat_name, $display_name, $date)));
        $response->headers->set('Content-Type', 'application/json');
    }
}