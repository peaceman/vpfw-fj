<?php
class App_DataObject_RuleViolation extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_RuleViolation
     */
    private $validator;

    /**
     * @var App_DataObject_Picture
     */
    private $picture;

    /**
     * @var App_DataObject_Session
     */
    private $session;

    /**
     * @param App_Validator_RuleViolation $validator
     * @param array $properties
     */
    public function __construct(App_Validator_RuleViolation $validator, $properties = null) {
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'PictureId' => null,
            'SessionId' => null,
            'Handled' => null,
            'Reason' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false);
        }
        parent::__construct($properties);
    }

    /**
     * @return int
     */
    public function getPictureId() {
        if (true == is_object($this->picture)) {
            return $this->picture->getId();
        } else {
            return $this->getData('PictureId');
        }
    }

    /**
     * @return App_DataObject_Picture
     */
    public function getPicture() {
        return $this->picture;
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
     * @return bool
     */
    public function getHandled() {
        return (bool)$this->getData('Handled');
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->getData('Reason');
    }

    /**
     * @param int $id
     * @param bool $validation
     */
    public function setPictureId($id, $validation = true) {
        if ($this->getPictureId() != $id) {
            if (true == $validation) {
                $this->validator->validatePictureId($id);
            }
            $this->setData('PictureId', $id);
            $this->setPicture(null);
        }
    }

    /**
     * @param App_DataObject_Picture
     */
    public function setPicture(App_DataObject_Picture $picture) {
        $this->picture = $picture;
        if (true == is_object($picture)) {
            $this->setData('PictureId', $picture);
        }
    }

    /**
     * @param int $id
     * @param bool $validation
     */
    public function setSessionId($id, $validation = true) {
        if ($this->getSessionId() != $id) {
            if (true == $validation) {
                $this->validator->validateSessionId($id);
            }
            $this->setData('SessionId', $id);
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
     * @param bool $handled
     * @param bool $validation
     */
    public function setHandled($handled, $validation = true) {
        if ($this->getHandled() != $handled) {
            if (true == $validation) {
                $this->validator->validateHandled($handled);
            }
            $this->setData('Handled', $handled);
        }
    }

    /**
     * @param string $reason
     * @param bool $validation
     */
    public function setReason($reason, $validation = true) {
        if ($this->getReason() != $reason) {
            if (true == $validation) {
                $this->validator->validateReason($reason);
            }
            $this->setData('Reason', $reason);
        }
    }
}
 
