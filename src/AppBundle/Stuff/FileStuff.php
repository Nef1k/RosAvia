<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 06.10.2016
 * Time: 11:51
 */

namespace AppBundle\Stuff;

use AppBundle\Entity\FileCategory;
use AppBundle\Entity\FileType;
use AppBundle\Entity\User;
use AppBundle\Entity\File;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class FileStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokens;

    private $router;

    /**
     * FileStuff constructor.
     * @param EntityManager $em
     * @param TokenStorage $tokenStorage
     */

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, Router $router)
    {
        $this->em = $em;
        $this->tokens = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @param $user_id
     * @param $file_cat_name
     * @param $disp_name
     * @param $file_date
     * @return bool
     */
    public function PushFile($user_id, $file_cat_name, $disp_name, $file_date)
    {
        /** @var  $user  User*/
        $user = $this->em->getRepository("AppBundle:User")->find($user_id);
        /** @var  $file_cat FileCategory*/
        $file_cat = $this->em->getRepository("AppBundle:FileCategory")->findBy(array("CategoryName" => $file_cat_name));
        if (!$file_cat) $file_cat = $this->em->getRepository("AppBundle:FileCategory")->find(0);
        /** @var  $file_type FileType*/
        $file_type = $this->em->getRepository("AppBundle:FileType")->find(0);
        $file = new File();
        $upload_dir = 'files/'.$user_id.'/';
        $file->
            setIDUser($user)->
            setRealCategory($file_cat_name)->
            setIDCategory($file_cat)->
            setIDFileType($file_type)->
            setFileName(basename($_FILES['userfile']['name']))->
            setFileSize($_FILES['userfile']['size'])->
            setDisplayName($disp_name)->
            setFileDate($file_date);
        if (!file_exists($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        $this->em->persist($file);
        $this->em->flush();
        $upload_file = $upload_dir.$file->getIDFile().'.'.$file_cat_name;

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file))
        {
            return true;
        }
        else
        {
            $this->em->remove($file);
            $this->em->flush();
            return false;
        }
    }

    /**
     * @param File $file
     * @param array $fields
     * @return array
     */
    public function FileToArray(File $file, array $fields){
        $file_info = [];
        if (in_array("file_name", $fields)){
            $file_info["file_name"] = $file->getFileName();
        }
        if (in_array("file_category", $fields)){
            $file_info["file_category"] = $file->getIDCategory()->getCategoryName();
        }
        if (in_array("file_type", $fields)){
            $file_info["file_type"] = $file->getIDFileType()->getTypeValue();
        }
        if (in_array("user_id", $fields)){
            $file_info["user_id"] = $file->getIDUser()->getIDUser();
        }
        if (in_array("display_name", $fields)){
            $file_info["display_name"] = $file->getDisplayName();
        }
        if (in_array("ID_File", $fields)){
            $file_info["ID_File"] = $file->getIDFile();
        }
        if (in_array("file_link", $fields)){
            $file_info["file_link"] = $this->router->generate("file_get",["ID_User" => $file->getIDUser()->getIDUser(), "ID_File" => $file->getIDFile()]);
        }
        return $file_info;
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $fields
     * @return array
     */
    public function GetFileArray(array $criteria, array $sort, array $fields){
        $files = $this->em->getRepository("AppBundle:File")->findBy($criteria, $sort);
        $file_list = [];
        if ($files != null){
            foreach($files AS $file){
                $file_info = $this->FileToArray($file, $fields);
                array_push($file_list, $file_info);
            }
        }
        return $file_list;
    }

    /**
     * @param $object
     * @return array
     */
    public function objectConvert($object){
        $criteria = [];
        /** @var  $user User*/
        $used = false;
        $user = $this->tokens->getToken()->getUser();
        $user_roles = (array) $this->tokens->getToken()->getUser();
        $user_ids = array();
        if (in_array("ROLE_ADMIN", $user_roles)){
            $used = false;
            /** @var  $users User[]*/
            $users = $this->em->getRepository("AppBundle:User")->findBy([],[]);
            foreach($users AS $dealer){
                array_push($user_ids, $dealer->getIDUser());
            }
        } elseif (in_array("ROLE_DEALER", $user_roles) and !($used)){
            array_push($user_ids, $user->getIDUser());
        }
        if ((isset($object["ID_File"])?$object["ID_File"]:null) != null) array_push($criteria["ID_File"],$object["ID_File"]);
        if ((isset($object["file_name"])?$object["file_name"]:null) != null) $criteria["file_name"] = $object["file_name"];
        if ((isset($object["ID_FileCategory"])?$object["ID_FileCategory"]:null) != null) $criteria["ID_FileCategory"] = $this->em->getRepository("AppBundle:FileCategory")->findBy(array("ID_FileCategory" => $object["ID_FileCategory"]));
        if ((isset($object["ID_User"])?$object["ID_User"]:null) != null) {
            $user_input = $object["ID_User"];
            $right_users = [];
            foreach($user_input AS $users_el){
                if (in_array($users_el, $user_ids)){
                    array_push($right_users, $users_el);
                }
            }
            if (count($right_users) != 0)
                $criteria["ID_User"] = $this->em->getRepository("AppBundle:User")->findBy(array("ID_User" => $right_users));
        } elseif (count($user_ids) != 0) {
            $criteria["ID_User"] = $user_ids;
        }
        return $criteria;
    }

    /**
     * @param $file
     */
    public function FileForceDownload($file, File $file_rec) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_rec->getFileName());
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }

    public function FileDelete($file_id) {
        /** @var  $file File*/
        $file = $this->em->getRepository("AppBundle:File")->find($file_id);
        if ($file) {
            $filename = 'files/' . $file->getIDUser()->getIDUser() . '/' . $file_id . '.' . $file->getRealCategory();
            if (file_exists($filename)) unlink($filename);
            $this->em->remove($file);
            $this->em->flush();
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function GetFileFromRequest($user_id, $file_id){
        $file = 'files';
        $Request_output = array(
            'error_msg' => array(),
            'error_param' => array()
        );
        $auth_user_id = $this->tokens->getToken()->getUser()->getIDUser();
        $auth_user_roles = $this->tokens->getToken()->getUser()->getRoles();
        if (($user_id == $auth_user_id) || (in_array('ROLE_ADMIN', $auth_user_roles))){
            if (file_exists($file)){
                $file = $file.'/'.$user_id;
                if (file_exists($file)) {
                    $user = $this->em->getRepository("AppBundle:User")->find($user_id);
                    if ($user) {
                        /**
                         * @var $files File[]
                         */
                        $files = $this->em->getRepository("AppBundle:File")->findBy(array('ID_File' => $file_id, 'ID_User' => $user));
                        if ($files) {
                            $file = $file . '/' . $files[0]->getIDFile().'.'.$files[0]->getRealCategory();
                            if (file_exists($file)) {
                                $this->FileForceDownload($file, $files[0]);
                            } else {
                                array_push($Request_output['error_msg'], 'Данный файл не существует.');
                            }
                        } else {
                            array_push($Request_output['error_msg'], 'Выбранный вами файл у этого пользователя отсутствует.');
                        }
                    }
                    else {
                        array_push($Request_output['error_msg'],'Данный пользователь не сущетвует!');
                    }
                }
                else{
                    array_push($Request_output['error_msg'],'У данного пользователя нет файлов.');
                }
            }
            else{
                array_push($Request_output['error_msg'], 'На сервере нет файлов.');
            }
        }
        else{
            array_push($Request_output['error_msg'], 'Вы не можете просматривать файлы этого пользователя.');
        }
        return $Request_output;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function GetFileArrayFromRequest(Request $request){
        $criteria = $this->objectConvert((array)json_decode($request->request->get('criteria')));
        $fields = json_decode($request->request->get('field_names'));
        $sort = (array)json_decode($request->request->get('sort'));
        if ($sort == null) $sort = [];
        if ($criteria == null) $criteria = [];
        if ($fields == null) $fields = [];
        $file = $this->GetFileArray($criteria, $sort, $fields);
        return $file;
    }

}