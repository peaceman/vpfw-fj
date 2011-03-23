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
     * @var App_DataMapper_Session
     */
    private $sessionMapper;

    /**
     * @var App_DataObject_Picture
     */
    private $picture;

    /**
     * @var App_DataMapper_Picture
     */
    private $pictureMapper;

    /**
     * @var App_DataObject_Deletion
     */
    private $deletion;

    /**
     * @var App_DataMapper_Deletion
     */
    private $deletionMapper;

    /**
     * @param App_Validator_PictureComment $validator
     * @param array $properties
     */
    public function __construct(App_DataMapper_Session $sessionMapper, App_DataMapper_Picture $pictureMapper, App_DataMapper_Deletion $deletionMapper, App_Validator_PictureComment $validator, $properties = null) {
        $this->sessionMapper = $sessionMapper;
        $this->pictureMapper = $pictureMapper;
        $this->deletionMapper = $deletionMapper;
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'SessionId' => null,
            'PictureId' => null,
            'DeletionId' => null,
            'Time' => null,
            'Text' => null,
        );
        $this->lazyLoadState = array(
            'Session' => false,
            'Picture' => false,
            'Deletion' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        $this->data['DeletionId']['required'] = false;
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

    private function lazyLoadPicture() {
        if (false === $this->lazyLoadState['Picture']) {
            $this->picture = $this->pictureMapper->getEntryById($this->getPictureId());
            $this->lazyLoadState['Picture'] = true;
        }
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
        if (true == is_null($this->deletion)) {
            $this->lazyLoadDeletion();
        }
        return $this->deletion;
    }

    private function lazyLoadDeletion() {
        if (false === $this->lazyLoadState['Deletion']) {
            $this->deletion = $this->deletionMapper->getEntryById($this->getDeletionId());
            $this->lazyLoadState['Deletion'] = true;
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
    public function setSession($session) {
        if (is_object($session)) {
            $this->proofObjectType('App_DataObject_Session', $session, __FUNCTION__);
            if ($this->getSessionId() != $session->getId()) {
                $this->setData('SessionId', $session->getId());
            }
        }
        $this->session = $session;
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
    public function setPicture($picture) {
        if (is_object($picture)) {
            $this->proofObjectType('App_DataObject_Picture', $picture, __FUNCTION__);
            if ($this->getPictureId() != $picture->getId()) {
                $this->setData('PictureId', $picture->getId());
            }
        }
        $this->picture = $picture;
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
        if (true == is_object($deletion)) {
            if ($this->getDeletionId() != $deletion->getId()) {
                $this->setData('DeletionId', $deletion->getId());
            }
        }
        $this->deletion = $deletion;
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
