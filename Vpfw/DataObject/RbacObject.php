<?php
class Vpfw_DataObject_RbacObject extends Vpfw_DataObject_Abstract {
    /**
     * @var Vpfw_Validator_RbacObject
     */
    private $validator;

    /**
     * @var Vpfw_DataObject_RbacPermission[]
     */
    private $permissions;

    /**
     * @var Vpfw_DataMapper_RbacPermission
     */
    private $permissionMapper;

    /**
     * @param Vpfw_DataMapper_Permission
     * @param Vpfw_Validator_RbacObject $validator
     * @param Vpfw_DataMapper_RbacRole $rbacRoleMapper
     * @param array|null $properties
     */
    public function __construct(Vpfw_DataMapper_RbacPermission $permissionMapper, Vpfw_Validator_RbacObject $validator, $properties = null) {
        $this->permissionMapper = $permissionMapper;
        $this->validator = $validator;
        $this->permissions = new Vpfw_ObserverArray();
        $this->data = array(
            'Id' => null,
            'Default' => null,
            'Name' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        $this->lazyLoadState = array(
            'Permissions' => false,
        );
        parent::__construct($properties);
    }

    public function getPermissions() {
        if (count($this->permissions) == 0) {
            $this->lazyLoadPermissions();
        }
        return $this->permissions->getArray();
    }

    private function lazyLoadPermissions() {
        if (false === $this->lazyLoadState['Permissions']) {
            $permissions = $this->permissionMapper->getEntriesByFieldValue(array('i|ObjectId|' . $this->getId()));
            foreach ($permissions as $permission) {
                $this->permissions[] = $permission;
            }
        }
    }

    public function getDefault() {
        $state = $this->getData('Default');
        if (true == is_null($state)) {
            return null;
        }
        if (0 === $this->getData('Default')) {
            return false;
        } else {
            return true;
        }
    }

    public function getName() {
        return $this->getData('Name');
    }

    public function setDefault($state, $validate = true) {
        if (false == is_bool($state)) {
            throw new Vpfw_Exception_InvalidArgument('Die Methode Vpfw_DataObject_RbacObject::setDefault akzeptiert nur boolsche Werte');
        }
        if ($state !== $this->getDefault()) {
            if (true == $state) {
                $this->setData('Default', 1);
            } else {
                $this->setData('Default', 0);
            }
        }
    }

    public function setName($name, $validate = true) {
        if ($this->getName() != $name) {
            if (true == $validate) {
                $this->validator->validateName($name);
            }
            $this->setData('Name', $name);
        }
    }
}
