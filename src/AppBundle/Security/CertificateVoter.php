<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Sertificate;
use AppBundle\Security\CertificateActionsChecker;

class CertificateVoter extends Voter
{
    const EDIT = "certificateEdit";
    const ACTIVATION_REQUEST = "certificateActivReq";
    const CLEAR = "certificateClear";

    private $checker;

    public function __construct(CertificateActionsChecker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(
            self::EDIT,
            self::ACTIVATION_REQUEST,
            self::CLEAR,
        ))){
            return false;
        }

        if (!$subject instanceof Sertificate){
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        $certificate = $subject;

        switch($attribute){
            case self::EDIT:
                return $this->checker->canPerform($user, $certificate, "edit");
            case self::ACTIVATION_REQUEST:
                return $this->checker->canPerform($user, $certificate, "activ_request");
            case self::CLEAR:
                return $this->checker->canPerform($user, $certificate, "clear");
        }
    }
}