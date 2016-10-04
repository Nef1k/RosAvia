<?php

namespace AppBundle\Stuff;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use AppBundle\Entity\User;

class ManagerStuff
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

    function getDealerTable($offset = 0, $amount = 15)
    {
        $sql = "
            SELECT
              *
            FROM
              dealer_summary
            WHERE
              ID_Mentor = ?
            ORDER BY
              `total_unpaid` DESC,
              `ID_Dealer`
        ";

        $query = $this->em->getConnection()->prepare($sql);
        $query->execute(array($this->getUser()->getIDUser()));
        return $query->fetchAll();
    }
}