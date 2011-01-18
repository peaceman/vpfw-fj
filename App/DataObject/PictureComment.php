<?php
class App_DataObject_PictureComment extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_PictureComment
     */
    private $validator;

    /**
     * @var App_DataObject_Session
     */
    private $session;

    /**
     * @var App_DataObject_Picture
     */
    private $picture;

    /**
     * @var App_DataObject_Deletion
     */
    private $deletion;

    /**
     * @param App_Validator_PictureComment $validator
     * @param array $properties
     */
    public function __construct(App_Validator_PictureComment $validator, $properties = null) {
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'SessionId' => null,
            'PictureId' => null,
            'DeletionId' => null,
            'Time' => null,
            'Text' => null,
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
    public function getDeletionId() {
        if (true == is_object($this->deletion)) {
            return $this->deletion->getId();
        } else {
            return $this->getData('DeletionId');
        }
    }

    /**
     * @return App_DataObject_Deletion
     */
    public function getDeletion() {
        return $this->deletion;
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
    public function getText() {
        return $this->getData('Text');
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
     * @param App_DataObject_Session $session
     */
    public function setSession(App_DataObject_Session $session) {
        $this->session = $session;
        if (true == is_object($session)) {
            $this->setData('SessionId', $session->getId());
        }
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
     * @param App_DataObject_Picture $picture
     */
    public function setPicture(App_DataObject_Picture $picture) {
        $this->picture = $picture;
        if (true == is_object($picture)) {
            $this->setData('PictureId', $picture->getId());
        }
    }

    /**
     * @param int $id
     * @param bool $validation
     */
    public function setDeletionId($id, $validation = true) {
        if ($this->getDeletionId() != $id) {
            if (true == $validation) {
                $this->validator->validateDeletionId($id);
            }
            $this->setData('DeletionId', $id);
            $this->setDeletion(null);
        }
    }

    /**
     * @param App_DataObject_Deletion $deletion
     */
    public function setDeletion(App_DataObject_Deletion $deletion) {
        $this->deletion = $deletion;
        if (true == is_object($deletion)) {
            $this->setData('DeletionId', $deletion->getId());
        }
    }

    /**
     * @param int $time
     * @param bool $validation
     */
    public function setTime($time, $validation = true) {
        if ($this->getTime() != $time) {
            if (true == $validation) {
                $this->validator->validateTime($time);
            }
            $this->setData('Time', $time);
        }
    }

    /**
     * @param string $text
     * @param bool $validation
     */
    public function setText($text, $validation = true) {
        if ($this->getText() != $text) {
            if (true == $validation) {
                $this->validator->validateText($text);
            }
            $this->setData('Text', $text);
        }
    }
}
