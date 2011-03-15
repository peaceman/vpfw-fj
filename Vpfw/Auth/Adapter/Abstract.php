<?php
abstract class Vpfw_Auth_Adapter_Abstract implements Vpfw_Auth_Adapter_Interface {
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var App_DataObject_User
     */
    protected $user;

    /**
     *
     * @param string $username
     * @return Vpfw_Auth_Adapter_Interface
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     *
     * @param string $password
     * @return Vpfw_Auth_Adapter_Interface
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    /**
     * @return string Das Passwort in gehashter Form
     */
    abstract protected function hashPassword();
}
