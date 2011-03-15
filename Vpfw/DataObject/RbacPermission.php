<?php
class Vpfw_DataObject_RbacPermission extends Vpfw_DataObject_Abstract {
    /**
     * @var Vpfw_DataObject_RbacRole
     */
    private $roleDao;

    /**
     * @var Vpfw_DataObject_RbacObject
     */
    private $objectDao;

    /**
     * @var Vpfw_DataMapper_RbacRole
     */
    private $roleMapper;

    /**
     * @var Vpfw_DataMapper_RbacObject
     */
    private $objectMapper;

    /**
     * @var Vpfw_Validator_RbacPermission
     */
    private $validator;

    /**
     * @param Vpfw_Validator_RbacPermission $validator
     * @param Vpfw_DataMapper_RbacRole $roleMapper
     * @param Vpfw_DataMapper_RbacObject $objectMapper
     * @param array $properties
     */
    public function __construct(Vpfw_Validator_RbacPermission $validator, Vpfw_DataMapper_RbacRole $roleMapper, Vpfw_DataMapper_RbacObject $objectMapper, $properties = null)  {
        $this->roleMapper = $roleMapper;
        $this->objectMapper = $objectMapper;
        $this->validator = $validator;
        $this->data = array(
            'Id' => null,
            'RoleId' => null,
            'ObjectId' => null,
            'State' => null,
        );
        $this->lazyLoadState = array(
            'Role' => false,
            'Object' => false,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => null);
        }
        parent::__construct($properties);
    }

    public function setRoleId($id, $validate = true) {
        if ($this->getRoleId() != $id) {
            $this->setData('RoleId', $id);
            $this->setRole(null);
        }
    }

    public function setRole(Vpfw_DataObject_RbacRole $role) {
        $this->roleDao = $role;
        if (true == is_object($role)) {
            self::checkDataObjectForId($role);
            $this->setData('RoleId', $role->getId());
        }
    }

    public function setObjectId($id, $validate = true) {
        if ($this->getObjectId() != $id) {
            $this->setData('ObjectId', $id);
            $this->setObject(null);
        }
    }

    public function setObject(Vpfw_DataObject_RbacObject $object) {
        $this->objectDao = $object;
        if (true == is_object($object)) {
            self::checkDataObjectForId($object);
            $this->setData('ObjectId', $object->getId());
        }
    }

    public function setState($state, $validate = true) {
        if ($this->getState() != $state) {
            if (true == $validate) {
                $this->validator->validateState($state);
            }
            if (false === $state) {
                $this->setData('State', 0);
            } else {
                $this->setData('State', 1);
            }
        }
    }

    /**
     * @return int
     */
    public function getRoleId() {
        if (true == is_object($this->roleDao)) {
            return $this->roleDao->getId();
        } else {
            return $this->getData('RoleId');
        }
    }

    /**
     * @return Vpfw_DataObject_RbacRole
     */
    public function getRole() {
        if (true == is_null($this->roleDao)) {
            $this->lazyLoadRole();
        }
        return $this->roleDao;
    }

    public function lazyLoadRole() {
        if (false === $this->lazyLoadState['Role']) {
            $this->roleDao = $this->roleMapper->getEntryById($this->getRoleId());
            $this->lazyLoadState['Role'] = true;
        }
    }

    /**
     * @return int
     */
    public function getObjectId() {
        if (true == is_object($this->objectDao)) {
            return $this->objectDao->getId();
        } else {
            return $this->getData('ObjectId');
        }
    }

    /**
     * @return Vpfw_DataObject_RbacObject
     */
    public function getObject() {
        if (true == is_null($this->objectDao)) {
            if (false === $this->lazyLoadState['Object']) {
                $this->lazyLoadObject();
            }
        }
        return $this->objectDao;
    }

    public function lazyLoadObject() {
        if (false === $this->lazyLoadState['Object']) {
            $this->object = $this->objectMapper->getEntryById($this->getObjectId());
            $this->lazyLoadState['Object'] = true;
        }
    }

    /**
     * @return bool
     */
    public function getState() {
        $state = $this->getData('State');
        if (0 === $state) {
            return false;
        } else {
            return true;
        }
    }
}