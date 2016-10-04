<?php

namespace AppBundle\Stuff;

use AppBundle\Entity\ParamValue;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use AppBundle\Entity\User;

class UserStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenSotrage;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this->em=$entityManager;
        $this->tokenSotrage = $tokenStorage;
    }

    /**
     * @return User
     */
    function getUser()
    {
        return $this->tokenSotrage->getToken()->getUser();
    }

    function getUserParam(User $user, $param_name)
    {
        $query = $this->em->createQuery("
            SELECT
              val.value
            FROM
              AppBundle:ParamValue val
            WHERE
              (val.ID_GroupParam = :param_name) AND
              (val.ID_User = :user_id)
        ");
        $query->setParameters(array(
            "param_name" => $param_name,
            "user_id" => $user->getIDUser(),
        ));

        return $query->getSingleScalarResult();
    }

    function getCurrentUserParam($param_name)
    {
        return $this->getUserParam($this->getUser(), $param_name);
    }

    function getDisplayName(User $user)
    {
        $name = $this->getUserParam($user, "name");
        $last_name = $this->getUserParam($user, "lastname");
        return $name." ".$last_name;
    }
    
    /**
     * @param User $user
     * @return array
     */
    function getUserParamList(User $user)
    {
        /** @var $user_params array */
        $user_params = array();

        /** @var $param_values ParamValue[] */
        $param_values = $this->em->getRepository("AppBundle:ParamValue")->findBy(array(
            "ID_User" => $user->getIDUser()
        ));

        foreach($param_values as $value){
            $user_params[$value->getGroupParam()->getName()] = $value->getValue();
        }

        return $user_params;
    }
}