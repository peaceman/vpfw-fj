<?php
class App_DataObject_User extends Vpfw_DataObject_Abstract implements Vpfw_Rbac_UserInterface {
    /**
     * @var App_Validator_User
     */
    private $validator;

    /**
     * @var App_DataObject_Deletion
     */
    private $deletion;

    /**
     * @var App_DataMapper_Deletion
     */
    private $deletionMapper;

    /**
     * @var App_DataMapper_Picture
     */
    private $pictureMapper;

    /**
     * @var App_DataObject_Picture[]
     */
    private $pictures = array();

    /**
     * @var App_DataMapper_PictureComment
     */
    private $pictureCommentMapper;

    /**
     * @var App_DataObject_PictureComment[]
     */
    private $pictureComments = array();

    /**
     * @var Vpfw_DataMapper_RbacRole
     */
    private $rbacRoleMapper;

    /**
     * @var Vpfw_DataObject_RbacRole[]
     */
    private $rbacRoles = array();

    /**
     * Befüllen von $this->data und weitergeben der Objekteigenschaften
     * an den Parentkonstruktor
     * @param App_Validator_User $validator
     * @param array $properties optional
     */
    public function __construct(Vpfw_DataMapper_RbacRole $rbacRoleMapper, App_DataMapper_PictureComment $pictureCommentMapper, App_DataMapper_Picture $pictureMapper, App_DataMapper_Deletion $deletionMapper, App_Validator_User $validator, $properties = null) {
        $this->rbacRoleMapper = $rbacRoleMapper;
        $this->pictureMapper = $pictureMapper;
        $this->deletionMapper = $deletionMapper;
        $this->pictureCommentMapper = $pictureCommentMapper;
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
        $this->lazyLoadState = array(
            'Deletion' => false,
            'Pictures' => false,
            'PictureComments' => false,
            'RbacRoles' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        $this->data['DeletionId']['required'] = false;
        parent::__construct($properties);
    }

    /**
     * @return Vpfw_DataObject_RbacRole[]
     */
    public function getRbacRoles() {
        if (empty($this->rbacRoles)) {
            $this->lazyLoadRbacRoles();
        }
        return $this->rbacRoles;
    }

    private function lazyLoadRbacRoles() {
        if (false === $this->lazyLoadState['RbacRoles']) {
            $this->rbacRoles = $this->rbacRoleMapper->getEntriesByUserId($this->getId());
            $this->lazyLoadState['RbacRoles'] = true;
        }
    }

    /**
     * @return App_DataObject_Pictures[]
     */
    public function getPictures() {
        if (true == empty($this->pictures)) {
            $this->lazyLoadPictures();
        }
        return $this->pictures;
    }

    /**
     * @todo Aktuell werden wenn man die Anzahl der Bilder ermitteln möchte,
     * alle Bilder aus der Datenbank geladen und in DataObjects verpackt, wobei
     * man eigentlich nur die Anzahl wissen möchte.
     * @return int
     */
    public function getPictureCount() {
        return count($this->getPictures());
    }

    /**
     * @return App_DataObject_PictureComment[]
     */
    public function getPictureComments() {
        if (true == empty($this->pictureComments)) {
            $this->lazyLoadPictureComments();
        }
        return $this->pictureComments;
    }

    private function lazyLoadPictureComments() {
        if (false === $this->lazyLoadState['PictureComments']) {
            $this->pictureComments = $this->pictureCommentMapper->getCommentsFromUserId($this->getId());
            $this->lazyLoadState['PictureComments'] = true;
        }
    }

    private function lazyLoadPictures() {
        if (false === $this->lazyLoadState['Pictures']) {
            $this->pictures = $this->pictureMapper->getEntriesByUserId($this->getId());
            $this->lazyLoadState['Pictures'] = true;
        }
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
        $netIp = $this->getData('CreationIp');
        return is_null($netIp) ? null : inet_ntop($netIp);
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
            $this->setData('CreationIp', inet_pton($ip));
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
     * @param App_DataObject_Deletion $deletion
     * @return void
     */
    public function setDeletion($deletion) {
        if (is_object($deletion)) {
            $this->proofObjectType('App_DataObject_Deletion', $deletion, __FUNCTION__);
            if ($this->getDeletionId() != $deletion->getId()) {
                $this->setData('DeletionId', $deletion->getId());
            }
        }
        $this->deletion = $deletion;
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
