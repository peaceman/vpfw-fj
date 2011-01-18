<?php
class App_DataObject_User extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_User
     */
    private $validator;
    
    /**
     * @var App_DataObject_Deletion
     */
    private $deletion;
    
    /**
     * Befüllen von $this->data und weitergeben der Objekteigenschaften
     * an den Parentkonstruktor
     * @param App_Validator_User $validator
     * @param array $properties optional 
     */
    public function __construct(App_Validator_User $validator, $properties = null) {
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'CreationTime' => null,
            'CreationIp' => null,
            'DeletionId' => null,
            'Username' => null,
            'Passhash' => null,
            'Email' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false);
        }
        parent::__construct($properties);
    }
    
    /**
     * Gibt den Zeitpunkt der Erstellung des Users als Timestamp zurück
     * @return int 
     */
    public function getCreationTime() {
        return $this->getData('CreationTime');
    }
    
    /**
     * Gibt die IP, die bei der Erstellung des Users verwendet wurde als
     * String zurück
     * @return string 
     */
    public function getCreationIp() {
        //TODO convert ip into human readable form
        return $this->getData('CreationIp');
    }
    
    /**
     * @return int 
     */
    public function getDeletionId() {
        if (true == is_object($this->deletion)) {
            return $this->deletion->getId();
        } else {
            return $this->getData('DeletionId');
        }
    }
    
    /**
     * @return string 
     */
    public function getUsername() {
        return $this->getData('Username');
    }
    
    /**
     * Gibt den MD5-Hash des Passwortes als Hexstring zurück
     * @return string 
     */
    public function getPasshash() {
        //TODO convert from bytecode into hex
        return $this->getData('Passhash');
    }
    
    /**
     * @return string 
     */
    public function getEmail() {
        return $this->getData('Email');
    }
    
    /**
     * @return App_DataObject_Deletion
     */
    public function getDeletion() {
        return $this->deletion;
    }
    
    /**
     * @param int $time
     * @param bool $validate 
     */
    public function setCreationTime($time, $validate = true) {
        if ($this->getCreationTime() != $time) {
            if (true == $validate) {
                $this->validator->validateCreationTime($time);
            }
            $this->setData('CreationTime', $time);
        }
    }
    
    /**
     * @param int $ip
     * @param bool $validate 
     */
    public function setCreationIp($ip, $validate = true) {
        if ($this->getCreationIp() != $ip) {
            if (true == $validate) {
                $this->validator->validateCreationIp($ip);
            }
            //TODO convert human readable ip into int
            $this->setData('CreationIp', $ip);
        }
    }
    
    /**
     * @param int $id
     * @param bool $validate 
     */
    public function setDeletionId($id, $validate = true) {
        if ($this->getDeletionId() != $id) {
            if (true == $validate) {
                $this->validator->validateDeletionId($id);
            }
            $this->setData('DeletionId', $id);
            $this->setDeletion(null);
        }
    }

    /**
     * @param App_DataObject_Deletion
     */
    public function setDeletion(App_DataObject_Deletion $deletion) {
        $this->deletion = $deletion;
        if (true == is_object($deletion)) {
            $this->setData('DeletionId', $deletion->getId());
        }
    }
    
    /**
     * @param string $name
     * @param bool $validate 
     */
    public function setUsername($name, $validate = true) {
        if ($this->getUsername() != $name) {
            if (true == $validate) {
                $this->validator->validateUsername($name);
            }
            $this->setData('Username', $name);
        }
    }
    
    /**
     * @param string $passhash
     * @param bool $validate
     */
    public function setPasshash($passhash, $validate = true) {
        if ($this->getPasshash() != $passhash) {
            if (true == $validate) {
                $this->validator->validatePasshash($passhash);
            }
            //TODO convert hex md5 string into bytecode
            $this->setData('Passhash', $passhash);
        }
    }
    
    /**
     * @param string $email
     * @param bool $validate
     */
    public function setEmail($email, $validate = true) {
        if ($this->getEmail() != $email) {
            if (true == $validate) {
                $this->validator->validateEmail($email);
            }
            $this->setData('Email', $email);
        }
    }
}
