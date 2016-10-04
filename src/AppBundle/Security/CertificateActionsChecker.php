<?php

namespace AppBundle\Security;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\SertAction;
use AppBundle\Entity\Sertificate;

class CertificateActionsChecker
{
    /**
     * @var \Doctrine\ORM\EntityManager entity manager instance
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function canPerform(User $user, Sertificate $certificate, $action)
    {
        /*
         * Сертификаты могут редактировать все администраторы и разработчики,
         * все менеджеры и дилер, который создал этот сертификат
         * При этом, если состояние сертификата выше "Заполнен",
         * то этот дилер теряет способность редактировтаь сертификат
         */

        $currentAction = $this->entityManager->getRepository("AppBundle:SertAction")->find($action);
        $user_group = $user->getUserGroup();

        //Check whether user's group have a permission to perform action on certificate
        if (!$currentAction->getAllowedGroups()->contains($user_group)){
            dump("Check whether user's group have a permission to perform action on certificate");
            return false;
        }

        //Check whether this action is allowed with the state certificate has
        if (! $currentAction->getStates()->contains($certificate->getSertState())){
            dump("Check whether this action is allowed with the state certificate has");
            return false;
        }

        //Check whether user has ability to edit others' certificates or it's his certificate
        if ( ($certificate->getUser()->getIDUser() != $user->getIDUser()) && (!$user_group->canEditOthers()) ){
            dump("Check whether user has ability to edit others' certificates or it's his certificate");
            return false;
        }

        return true;
    }
}