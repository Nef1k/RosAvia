<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 06.10.2016
 * Time: 11:51
 */

namespace AppBundle\Stuff;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * FileStuff constructor.
     * @param EntityManager $em
     * @param TokenStorage $tokenStorage
     */

    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->tokens = $tokenStorage;
    }

    /**
     * @param $user_id
     * @param $id_file_category
     * @return bool
     */
    public function PushFile($user_id, $id_file_category)
    {
        $user = $this->em->getRepository("AppBundle:User")->find($user_id);
        $file_cat = $this->em->getRepository("AppBundle:FileCategory")->find($id_file_category);
        $file = new File();
        $upload_dir = '/files/'.$user_id;
        $upload_file = $upload_dir.basename($_FILES['userfile']['name']);
        $file->
            setUser($user)->
            setFileCategory($file_cat)->
            setFileName(basename($_FILES['userfile']['name']))->
            setFileSize($_FILES['userfile']['size']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file)){
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param File $cert
     * @param array $fields
     * @return array
     */
    public function CertToArray(File $cert, array $fields){
        $cert_info = [];
        if (in_array("file_name", $fields)){
            $cert_info["file_name"] = $cert->getFileName();
        }
        if (in_array("file_category", $fields)){
            $cert_info["file_category"] = $cert->getFileCategory()->getName();
        }
        if (in_array("user_id", $fields)){
            $cert_info["user_id"] = $cert->getUser()->getIDUser();
        }
        if (in_array("ID_File", $fields)){
            $cert_info["ID_File"] = $cert->getIDFile();
        }
        return $cert_info;
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $fields
     * @return array
     */
    public function GetFileArray(array $criteria, array $sort, array $fields){
        $certs = $this->em->getRepository("AppBundle:File")->findBy($criteria, $sort);
        $cert_list = [];
        if ($certs != null){
            foreach($certs AS $cert){
                $cert_info = $this->CertToArray($cert, $fields);
                array_push($cert_list, $cert_info);
            }
        }
        return $cert_list;
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
     * @param Request $request
     * @return array
     */
    public function GetFileArrayFromRequest(Request $request){
        $criteria = $this->objectConvert((array)json_decode($request->request->get('criteria')));
        $fields = json_decode($request->request->get('field_names'));
        $sort = json_decode($request->request->get('sort'));
        if ($sort == null) $sort = [];
        if ($criteria == null) $criteria = [];
        if ($fields == null) $fields = [];
        $cert = $this->GetFileArray($criteria, $sort, $fields);
        return $cert;
    }

}