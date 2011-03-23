<?php
class App_DataObject_Picture extends Vpfw_DataObject_Abstract {
    /**
     * @var App_Validator_Picture
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
     * @var App_DataObject_Deletion
     */
    private $deletion;

    /**
     * @var App_DataMapper_Deletion
     */
    private $deletionMapper;

    /**
     * @var App_DataMapper_PictureComment
     */
    private $commentMapper;

    /**
     * @var array
     */
    private $comments;

    /**
     * @param App_Validator_Picture $validator
     * @param array $properties
     */
    public function __construct(App_DataMapper_Session $sessionMapper, App_DataMapper_Deletion $deletionMapper, App_Validator_Picture $validator, App_DataMapper_PictureComment $commentMapper, $properties = null) {
        $this->sessionMapper = $sessionMapper;
        $this->deletionMapper = $deletionMapper;
        $this->validator = $validator;
        $this->commentMapper = $commentMapper;
        $this->data = array(
            'Id' => null,
            'Md5' => null,
            'Gender' => null,
            'SessionId' => null,
            'UploadTime' => null,
            'SiteHits' => null,
            'PositiveRating' => null,
            'NegativeRating' => null,
            'DeletionId' => null,
        );
        $this->lazyLoadState = array(
            'Session' => false,
            'Deletion' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        $this->data['DeletionId']['required'] = false;
        $this->data['SiteHits']['required'] = false;
        $this->data['PositiveRating']['required'] = false;
        $this->data['NegativeRating']['required'] = false;
        parent::__construct($properties);
    }

    /**
     * @return array
     */
    public function getComments() {
        if (true == is_null($this->comments)) {
            $this->comments = $this->commentMapper->getEntriesByFieldValue(array('i|PictureId|' . $this->getId()));
        }
        return $this->comments;
    }

    public function getNumberOfComments() {
        return $this->commentMapper->getNumberOfFieldValue(array('i|PictureId|' . $this->getId()));
    }

    /**
     * @return string
     */
    public function getMd5() {
        return $this->getData('Md5');
    }

    /**
     * @return string
     */
    public function getGender() {
        $tmpGender = $this->getData('Gender');
        if (0 === $tmpGender) {
            return 'male';
        } elseif (1 === $tmpGender) {
            return 'female';
        }
        return null;
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
    public function getUploadTime() {
        return $this->getData('UploadTime');
    }

    /**
     * @return int
     */
    public function getSiteHits() {
        return $this->getData('SiteHits');
    }

    /**
     * @return int
     */
    public function getPositiveRating() {
        return $this->getData('PositiveRating');
    }

    /**
     * @return int
     */
    public function getNegativeRating() {
        return $this->getData('NegativeRating');
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
     * @param string $md5
     * @param bool $validation
     */
    public function setMd5($md5, $validation = true) {
        if ($this->getMd5() != $md5) {
            if (true == $validation) {
                $this->validator->validateMd5($md5);
            }
            $this->setData('Md5', $md5);
        }
    }

    /**
     * @param int $gender
     * @param bool $validation
     */
    public function setGender($gender, $validation = true) {
        if ($this->getGender() != $gender) {
            if (true == $validation) {
                $this->validator->validateGender($gender);
            }
            switch ($gender) {
                case 'male':
                    $this->setData('Gender', 0);
                    break;
                case 'female':
                    $this->setData('Gender', 1);
                    break;
            }
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
        if (true == is_object($session)) {
            $this->proofObjectType('App_DataObject_Session', $session, __FUNCTION__);
            if ($this->getSessionId() != $session->getId()) {
                $this->setData('SessionId', $session->getId());
            }
        }
        $this->session = $session;
    }

    /**
     * @param int $time
     * @param bool $validation
     */
    public function setUploadTime($time, $validation = true) {
        if ($this->getUploadTime() != $time) {
            if (true == $validation) {
                $this->validator->validateUploadTime($time);
            }
            $this->setData('UploadTime', $time);
        }
    }

    /**
     * @param int $hits
     * @param bool $validation
     */
    public function setSiteHits($hits, $validation = true) {
        if ($this->getSiteHits() != $hits) {
            if (true == $validation) {
                $this->validator->validateSiteHits($hits);
            }
            $this->setData('SiteHits', $hits);
        }
    }

    public function increaseSiteHits() {
        $this->setSiteHits($this->getSiteHits() + 1);
    }

    public function increaseRating() {
        $this->setPositiveRating($this->getPositiveRating() + 1);
    }

    public function decreaseRating() {
        $this->setNegativeRating($this->getNegativeRating() + 1);
    }

    /**
     * @param int $rating
     * @param bool $validation
     */
    public function setPositiveRating($rating, $validation = true) {
        if ($this->getPositiveRating() != $rating) {
            if (true == $validation) {
                $this->validator->validatePositiveRating($rating);
            }
            $this->setData('PositiveRating', $rating);
        }
    }

    /**
     * @param int $rating
     * @param bool $validation
     */
    public function setNegativeRating($rating, $validation = true) {
        if ($this->getNegativeRating() != $rating) {
            if (true == $validation) {
                $this->validator->validateNegativeRating($rating);
            }
            $this->setData('NegativeRating', $rating);
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
    public function setDeletion($deletion) {
        if (true == is_object($deletion)) {
            $this->proofObjectType('App_DataObject_Deletion', $deletion, __FUNCTION__);
            if ($this->getDeletionId() != $deletion->getId()) {
                $this->setData('DeletionId', $deletion->getId());
            }
        }
        $this->deletion = $deletion;
    }

    public function getRating() {
        return $this->getPositiveRating() - $this->getNegativeRating();
    }
}

