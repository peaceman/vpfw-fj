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
     * Befüllen von $this->data und weitergeben der Objekteigenschaften an den
     * Parentkonstruktor
     * @param App_Validator_Deletion $validator
     * @param array $properties optional
     */
    public function __construct(App_Validator_Deletion $validator, $properties = null) {
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'SessionId' => null,
            'Time' => null,
            'Reason' => null
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false);
        }
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
        return $this->session;
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
    public function setSession(App_DataObject_Session $session) {
        $this->session = $session;
        if (true == is_object($session)) {
            $this->setData('SessionId', $session->getId());
        }
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
 
