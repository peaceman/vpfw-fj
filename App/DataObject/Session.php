<?php
class App_DataObject_Session extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_Session
     */
    private $validator;

    /**
     * @var App_DataObject_User
     */
    private $user;

    /**
     * @param App_Validator_Session $validator
     * @param array $properties
     * @return void
     */
    public function __construct(App_Validator_Session $validator, $properties = null) {
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'UserId' => null,
            'Ip' => null,
            'StartTime' => null,
            'LastRequest' => null,
            'Hits' => null,
            'UserAgent' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => null);
        }
        parent::__construct($properties);
    }

    /**
     * @return int
     */
    public function getUserId() {
        if (true == is_object($this->user)) {
            return $this->user->getId();
        } else {
            return $this->getData('UserId');
        }
    }

    /**
     * @return App_DataObject_User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getIp() {
        //TODO convert ip into human readable form
        return $this->getData('Ip');
    }

    /**
     * @return int
     */
    public function getStartTime() {
        return $this->getData('StartTime');
    }

    /**
     * @return int
     */
    public function getLastRequest() {
        return $this->getData('LastRequest');
    }

    /**
     * @return int
     */
    public function getHits() {
        return $this->getData('Hits');
    }

    /**
     * @return string
     */
    public function getUserAgent() {
        return $this->getData('UserAgent');
    }

    /**
     * @param int $id
     * @param bool $validate
     * @return void
     */
    public function setUserId($id, $validate = true) {
        if ($this->getUserId() != $id) {
            if (true == $validate) {
                $this->validator->validateUserId($id);
            }
            $this->setData('UserId', $id);
            $this->setUser(null);
        }
    }

    /**
     * @param App_DataObject_User $user
     * @return void
     */
    public function setUser(App_DataObject_User $user) {
        $this->user = $user;
        if (true == is_object($user)) {
            $this->setData('UserId', $user->getId());
        }
    }

    /**
     * @param string $ip
     * @param bool $validate
     * @return void
     */
    public function setIp($ip, $validate = true) {
        if ($this->getIp() != $ip) {
            if (true == $validate) {
                $this->validator->validateIp($ip);
            }
            //TODO convert ip from human readable form into an integer
            $this->setData('Ip', $ip);
        }
    }

    /**
     * @param int $time
     * @param bool $validation
     * @return void
     */
    public function setStartTime($time, $validation = true) {
        if ($this->getStartTime() != $time) {
            if (true == $validation) {
                $this->validator->validateStartTime($time);
            }
            $this->setData('StartTime', $time);
        }
    }

    /**
     * @param int $time
     * @param bool $validation
     * @return void
     */
    public function setLastRequest($time, $validation = true) {
        if ($this->getLastRequest() != $time) {
            if (true == $validation) {
                $this->validator->validateLastRequest($time);
            }
            $this->setData('LastRequest', $time);
        }
    }

    /**
     * @param int $hits
     * @param bool $validation
     * @return void
     */
    public function setHits($hits, $validation = true) {
        if ($this->getHits() != $hits) {
            if (true == $validation) {
                $this->validator->validateHits($hits);
            }
            $this->setData('Hits', $hits);
        }
    }

    /**
     * @param string $userAgent
     * @param bool $validation
     * @return void
     */
    public function setUserAgent($userAgent, $validation = true) {
        if ($this->getUserAgent() != $userAgent) {
            if (true == $validation) {
                $this->validator->validateUserAgent($userAgent);
            }
            $this->setData('UserAgent', $userAgent);
        }
    }
}
 
