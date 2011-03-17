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
     * @var App_DataMapper_Session
     */
    private $sessionMapper;

    /**
     * @var App_DataMapper_Picture
     */
    private $pictureMapper;

    /**
     * @param App_Validator_RuleViolation $validator
     * @param array $properties
     */
    public function __construct(App_DataMapper_Session $sessionMapper, App_DataMapper_Picture $pictureMapper, App_Validator_RuleViolation $validator, $properties = null) {
        $this->validator = $validator;
        $this->sessionMapper = $sessionMapper;
        $this->pictureMapper = $pictureMapper;
        
        $this->data = array(
            'Id' => null,
            'PictureId' => null,
            'SessionId' => null,
            'Time' => null,
            'Handled' => null,
            'Reason' => null,
        );
        $this->lazyLoadState = array(
            'Session' => false,
            'Picture' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
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
        if (true == is_null($this->picture)) {
            $this->lazyLoadPicture();
        }
        return $this->picture;
    }

    public function lazyLoadPicture() {
        if (false === $this->lazyLoadState['Picture']) {
            $this->picture = $this->pictureMapper->getEntryById($this->getPictureId());
            $this->lazyLoadState['Picture'] = true;
        }
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

    public function lazyLoadSession() {
        if (false === $this->lazyLoadState['Session']) {
            $this->session = $this->sessionMapper->getEntryById($this->getSessionId());
            $this->lazyLoadState['Session'] = true;
        }
    }

    public function getTime() {
        return $this->getData('Time');
    }

    /**
     * @return bool
     */
    public function getHandled() {
        return $this->getData('Handled');
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
    public function setPicture($picture) {
        $this->picture = $picture;
        if (true == is_object($picture)) {
            if ($this->getPictureId() != $picture->getId())
                $this->setData('PictureId', $picture->getId());
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
    public function setSession($session) {
        $this->session = $session;
        if (true == is_object($session)) {
            if ($this->getSessionId() != $session->getId())
                $this->setData('SessionId', $session->getId());
        }
    }

    /**
     * @param bool $handled
     * @param bool $validation
     */
    public function setHandled($handled, $validation = true) {
        if ($this->getHandled() !== $handled) {
            if (true == $validation) {
                $this->validator->validateHandled($handled);
            }
            $this->setData('Handled', $handled);
        }
    }

    public function setTime($time, $validation = true) {
        if ($this->getTime() != $time) {
            if (true == $validation) {
                $this->validator->validateTime($time);
            }
            $this->setData('Time', $time);
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
 
