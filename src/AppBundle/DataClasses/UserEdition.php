<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 18.09.2016
 * Time: 14:29
 */

namespace AppBundle\DataClasses;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as MyAssert;


class UserEdition
{
    /**
     *      @Assert\NotBlank(message="Значение не может быть пустым."),
     *      @Assert\Type(
     *          type="digit",
     *          message="Значение не может быть не числом.")
     *      @MyAssert\CheckUserID()
     *      @Assert\NotNull(message = "Значение не может быть нулевым")
     */
    protected $user_id;


    /**
     *      @Assert\NotBlank(message="Значение не может быть пустым."),
     *      @Assert\Type(
     *          type="digit",
     *          message="Значение не может быть не числом.")
     *      @MyAssert\CheckUserGroupID()
     *      @Assert\NotNull(message = "Значение не может быть нулевым")
     */
    protected $user_group_id;

    /**
     * @return mixed
     */
    public function getUserGroupId()
    {
        return $this->user_group_id;
    }

    /**
     * @param mixed $user_group_id
     * @return $this
     */
    public function setUserGroupId($user_group_id)
    {
        $this->user_group_id = $user_group_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }
}