<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 13.10.2016
 * Time: 22:37
 */

namespace AppBundle\Controller;

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
     * Method("POST")
     * Route("/files/attach"), name="file_attach")
     * @return Response
     */
    public function setUserFileAction(Request $request){
        /** @var $file_stuff FileStuff */
        $file_stuff = $this->get("app.certificate_stuff");
        $response = new Response();
        $response->setContent(json_encode($file_stuff->GetFileFromRequest($request)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}