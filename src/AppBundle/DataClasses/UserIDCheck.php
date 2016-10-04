<?php


namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CheckAssert;

class UserIDCheck
{
    /**
     *      @Assert\NotBlank(message="Значение не может быть пустым."),
     *      @Assert\Type(
     *          type="digit",
     *          message="Значение не может быть не числом.")
     *      @CheckAssert\CheckUserID()
     *      @Assert\NotNull(message = "Значение не может быть нулевым")
     */
    protected $User_ID;

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->User_ID;
    }

    /**
     * @param mixed $User_ID
     * @return $this
     */
    public function setUserID($User_ID)
    {
        $this->User_ID = $User_ID;
        return $this;
    }

}