<?php
class Vpfw_Auth_Adapter_Database extends Vpfw_Auth_Adapter_Abstract {
    /**
     * @var App_DataMapper_User
     */
    private $userMapper;

    /**
     *
     * @param App_DataMapper_User $userMapper
     */
    public function __construct(App_DataMapper_User $userMapper) {
        $this->userMapper = $userMapper;
    }

    public function areCredentialsValid() {
        if (true == is_null($this->username)) {
            throw new Vpfw_Exception_Logical('Es ist nicht möglich den Usernamen zu überprüfen, wenn er nicht gesetzt wurde');
        }
        if (true == is_null($this->password)) {
            throw new Vpfw_Exception_Logical('Es ist nicht möglich das Password zu überprüfen, wenn es nicht gesetzt wurde');
        }

        $userDao = $this->userMapper->getEntriesByFieldValue(array('s|Username|' . $this->username));
        if (0 == count($userDao)) {
            return false;
        } else {
            $userDao = $userDao[0];
        }
        $hashedInputPassword = $this->hashPassword();
        if ($userDao->getPasshash() != $hashedInputPassword) {
            return false;
        } else {
            $this->user = $userDao;
            return true;
        }
    }

    public function hashPassword() {
        return md5($this->password);
    }
}
