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
     * @var App_DataObject_Deletion
     */
    private $deletion;

    /**
     * @param App_Validator_Picture $validator
     * @param array $properties
     */
    public function __construct(App_Validator_Picture $validator, $properties = null) {
        $this->validator = $validator;
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
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false);
        }
        parent::__construct($properties);
    }

    /**
     * @return string
     */
    public function getMd5() {
        //TODO convert binary md5 into string
        return $this->getData('Md5');
    }

    /**
     * @return int
     */
    public function getGender() {
        return $this->getData('Gender');
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
        return $this->deletion;
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
            //TODO convert from string to binary
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
            $this->setData('Gender', $gender);
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
    public function setDeletion(App_DataObject_Deletion $deletion) {
        $this->deletion = $deletion;
        if (true == is_object($deletion)) {
            $this->setData('DeletionId', $deletion->getId());
        }
    }
}
 
