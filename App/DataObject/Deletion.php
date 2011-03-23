<?php
class App_DataObject_Deletion extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_Deletion
     */
    private $validator;

    /**
     * @var App_DataObject_Session
     */
    private $session;

    /**
     * @var App_DataMapper_Session
     */
    private $sessionMapper;

    /**
     * Befüllen von $this->data und weitergeben der Objekteigenschaften an den
     * Parentkonstruktor
     * @param App_Validator_Deletion $validator
     * @param array $properties optional
     */
    public function __construct(App_DataMapper_Session $sessionMapper, App_Validator_Deletion $validator, $properties = null) {
        $this->validator = $validator;
        $this->sessionMapper = $sessionMapper;

        $this->data = array(
            'Id' => null,
            'SessionId' => null,
            'Time' => null,
            'Reason' => null
        );
        $this->lazyLoadState = array(
            'Session' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        $this->data['Reason']['required'] = false;
        parent::__construct($properties);
    }

    /**
     * @return int
     */
    public function getSessionId() {
        if (true == is_object($this->session)) {
            return $this->session->getId();
        } else {
            return $this->getData('SessionId');
        }
    }

    /**
     * @return App_DataObject_Session
     */
    public function getSession() {
        if (true == is_null($this->session)) {
            $this->lazyLoadSession();
        }
        return $this->session;
    }

    private function lazyLoadSession() {
        if (false === $this->lazyLoadState['Session']) {
            $this->session = $this->sessionMapper->getEntryById($this->getSessionId());
            $this->lazyLoadState['Session'] = true;
        }
    }

    /**
     * @return int
     */
    public function getTime() {
        return $this->getData('Time');
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->getData('Reason');
    }

    /**
     * @param int $sessionId
     * @param bool $validate
     */
    public function setSessionId($sessionId, $validate = true) {
        if ($this->getSessionId() != $sessionId) {
            if (true == $validate) {
                $this->validator->validateSessionId($sessionId);
            }
            $this->setData('SessionId', $sessionId);
            $this->setSession(null);
        }
    }

    /**
     * @param App_DataObject_Session
     */
    public function setSession($session) {
        if (true == is_object($session)) {
            $this->proofObjectType('App_DataObject_Session', $session, __FUNCTION__);
            if ($this->getSessionId() != $session->getId())
                $this->setData('SessionId', $session->getId());
        }
        $this->session = $session;
    }

    /**
     * @param int $time
     * @param bool $validate
     */
    public function setTime($time, $validate = true) {
        if ($this->getTime() != $time) {
            if (true == $validate) {
                $this->validator->validateTime($time);
            }
            $this->setData('Time', $time);
        }
    }

    /**
     * @param string $reason
     * @param bool $validate
     */
    public function setReason($reason, $validate = true) {
        if ($this->getReason() != $reason) {
            if (true == $validate) {
                $this->validator->validateReason($reason);
            }
            $this->setData('Reason', $reason);
        }
    }
}

