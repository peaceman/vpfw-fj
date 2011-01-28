<?php
interface Vpfw_Auth_Adapter_Interface {
    /**
     * @param string $username
     * @return Vpfw_Auth_Adapter_Interface
     */
    public function setUsername($username);

    /**
     * @param string $password Das ungehashte Passwort
     * @return Vpfw_Auth_Adapter_Interface
     */
    public function setPassword($password);

    /**
     * @return bool
     */
    public function areCredentialsValid();

    /**
     * @return App_DataObject_User
     */
    public function getUser();
}