<?php
class Vpfw_Auth_Session {
    /**
     * @var Vpfw_Auth_Storage_Interface
     */
    private $storage;

    /**
     * @var Vpfw_Auth_Adapter_Interface
     */
    private $adapter;
    
    /**
     * @var App_DataMapper_Session
     */
    private $sessionMapper;

    /**
     * @var App_DataObject_Session
     */
    private $session;

    /**
     * @var Vpfw_Request_Interface
     */
    private $request;

    /**
     * @var Vpfw_Rbac_User
     */
    private $rbacUser;

    /**
     * @param Vpfw_Auth_Adapter_Interface $adapter
     * @param Vpfw_Auth_Storage_Interface $storage
     * @param App_DataMapper_Session $sessionMapper
     * @param Vpfw_Request_Interface $request
     */
    public function __construct(Vpfw_Auth_Adapter_Interface $adapter, Vpfw_Auth_Storage_Interface $storage, App_DataMapper_Session $sessionMapper, Vpfw_Request_Interface $request) {
        $this->adapter = $adapter;
        $this->storage = $storage;
        $this->sessionMapper = $sessionMapper;
        $this->request = $request;
        $this->reattachOldOrCreateNewSession();
    }

    private function reattachOldOrCreateNewSession() {
        if (true == is_null($this->storage->get('DbSesId'))) {
            $this->createNewSession();
        } else {
            $this->tryToReattachOldSession();
        }
    }

    private function createNewSession() {
        $sessionData = array(
            'Ip' => $this->request->getRemoteAddress(),
            'StartTime' => time(),
            'LastRequest' => time(),
            'Hits' => 1,
            'UserAgent' => $this->request->getHeader('user-agent')
        );

        $this->session = $this->sessionMapper->createEntry($sessionData, true);
        $this->storage->set('DbSesId', $this->session->getId());
    }

    private function tryToReattachOldSession() {
        if (false == $this->tryToGetSessionFromDatabase()) {
            $this->createNewSession();
        } elseif (true == $this->isOldSessionHijacked()) {
            $this->createNewSession();
        } else {
            $this->reattachOldSession();
        }
    }

    private function tryToGetSessionFromDatabase() {
        try {
            $this->session = $this->sessionMapper->getEntryById($this->storage->get('DbSesId'));
        } catch (Vpfw_Exception_OutOfRange $e) {
            return false;
        }
        return true;
    }

    private function isOldSessionHijacked() {
        if ($this->session->getIp() != $this->request->getRemoteAddress()) {
            return true;
        }
        if ($this->session->getUserAgent() != $this->request->getHeader('user-agent')) {
            return true;
        }
        return false;
    }

    private function reattachOldSession() {
        $this->session->setLastRequest(time());
        $this->session->setHits($this->session->getHits() + 1);
    }

    /**
     *
     * @return Vpfw_Rbac_User
     */
    public function getRbacUser() {
        if (true == is_null($this->rbacUser)) {
            $this->rbacUser = Vpfw_Factory::getRbacUser($this->session->getUser());
        }
        return $this->rbacUser;
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
    public function isLoggedIn() {
        if (false == is_null($this->session->getUser())) {
            return true;
        }
        return false;
    }

    public function login($username, $password) {
        if (false == is_null($this->session->getUser())) {
            $this->createNewSession();
        }
        $this->adapter->setUsername($username)->setPassword($password);
        if (true == $this->adapter->areCredentialsValid()) {
            $user = $this->adapter->getUser();
            $this->session->setUser($user);
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $this->createNewSession();
    }

    public function set($name, $value) {
        $this->storage->set($name, $value);
    }

    public function get($name) {
        return $this->storage->get($name);
    }
}
