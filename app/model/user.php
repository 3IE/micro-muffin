<?php
/**
 * User: mathieu.savy
 * Date: 05/06/13
 * Time: 16:03
 */

class User extends \Lib\Model
{
    /** @var  string */
    private $_login;
    /** @var  string */
    private $_password;

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->_login = $login;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }
}