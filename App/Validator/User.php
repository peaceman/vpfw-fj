<?php
class App_Validator_User {
    /**
     * @var App_DataMapper_User
     */
    private $userMapper;
    
    /**
     * @param App_DataMapper_User
     */
    public function __construct(App_DataMapper_User $userMapper) {
        $this->userMapper = $userMapper;
    }

    /**
     * Prüft ob der Username den Längenvorgaben entspricht
     * und ob dieser Name nicht bereits in der Datenbank existiert.
     *
     * @param string $name
     */
    public function validateUsername($name) {
        $nameLen = strlen($name);
        if (3 > $nameLen || 32 < $nameLen) {
            throw new Vpfw_Exception_Validation('Der Benutzername muss mindestens 3 und maximal 32 Zeichen lang sein');
        }

        if (true == $this->userMapper->entryWithFieldValuesExists(array('s|Username|' . $name))) {
            throw new Vpfw_Exception_Validation('Es existiert bereits ein Benutzer mit dem Namen ' . $name);
        }
    }

    public function validateEmail($email) {
        $emailLen = strlen($email);
        if (3 > $emailLen || 128 < $emailLen) {
            throw new Vpfw_Exception_Validation('Die Email muss mindestens 3 und maximal 128 Zeichen lang sein');
        }
        if (false == filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Vpfw_Exception_Validation('Die Email-Adresse ist ungültig');
        }
        if (true == $this->userMapper->entryWithFieldValuesExists(array('s|Email|' . $email))) {
            throw new Vpfw_Exception_Validation('Es existiert bereits ein Benutzer mit der Email-Adresse ' . $email);
        }
    }

    public function validatePasshash($hash) {
        if (false == (!empty($hash) && 0 !== preg_match('/^[a-f0-9]{32}$/', $hash))) {
            throw new Vpfw_Exception_Validation('Der MD5-Hash des Passwortes ist ungültig');
        }
    }

    public function validateCreationIp($ip) {
        if (false == filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new Vpfw_Exception_Validation('Die IP-Adresse ' . $ip . ' ist ungültig');
        }
    }

    public function validateCreationTime($timestamp) {
        if (((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX)) {
            throw new Vpfw_Exception_Validation('Der Timestamp ' . $time . ' ist ungültig');
        }
    }
}
