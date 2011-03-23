<?php
class Vpfw_DataObject_RbacRole extends Vpfw_DataObject_Abstract {
    /**
     * @var Vpfw_Validator_RbacRole
     */
    private $validator;

    /**
     * @var Vpfw_DataObject_RbacPermission[]
     */
    private $permissions;

    /**
     * @var Vpfw_DataObject_RbacPermissions[]
     */
    private $permissionsByName;

    /**
     * @var Vpfw_DataMapper_RbacPermission
     */
    private $permissionMapper;

    /**
     * Signalisiert ob die Permissions bereits mit Hilfe des lazy loadings
     * geladen wurden.
     *
     * @var bool
     */
    private $permissionsWereLoaded = false;

    /**
     *
     * @param array $properties
     */
    public function __construct(Vpfw_DataMapper_RbacPermission $permissionMapper, Vpfw_Validator_RbacRole $validator, $properties = null) {
        $this->validator = $validator;
        $this->permissionMapper = $permissionMapper;
        $this->permissions = new Vpfw_ObserverArray();
        $this->permissionsByName = new Vpfw_ObserverArray();
        $this->data = array(
            'Id' => null,
            'Name' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => null);
        }
        parent::__construct($properties);
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->getData('Name');
    }

    /**
     * Setzt den Namen für die RbacRole
     *
     * @param string $name
     * @param bool $validate
     * @return Vpfw_DataObject_RbacRole
     */
    public function setName($name, $validate = true) {
        if ($this->getName() != $name) {
            if (true == $validate) {
                $this->validator->validateName($name);
            }
            $this->setData('Name', $name);
        }
        return $this;
    }

    /**
     * Ermöglicht das Setzen der RbacPermissions in der RbacRole, dabei werden
     * alle bisher abgelegten RbacPermissions werden aus diesem Objekt
     * gelöscht. Die RbacPermissions werden selbst aber nicht gelöscht.
     *
     * @param Vpfw_DataObject_RbacPermisson[] $permissions
     */
    public function setPermissions(array $permissions) {
        $this->permissions = array();
        foreach ($permissions as $permission) {
            $this->proofObjectType('Vpfw_DataObject_RbacPermission', $permission, __FUNCTION__);
            self::checkDataObjectForId($permission);
            $this->permissions[$permission->getId()] = $permission;
            $this->permissionsByName[$permission->getObject()->getName()] = $permission;
        }
    }

    /**
     * Fügt eine RbacPermission dieser RbacRole hinzu.
     * Benutzt lazyLoadPermissions
     *
     * @param Vpfw_DataObject_RbacPermission $permission
     * @return Vpfw_DataObject_RbacRole
     */
    public function addPermission(Vpfw_DataObject_RbacPermission $permission) {
        self::checkDataObjectForId($permission);
        $this->lazyLoadPermissions();
        $this->permissions[$permission->getId()] = $permission;
        $this->permissionsByName[$permission->getObject()->getName()] = $permission;
        return $this;
    }

    /**
     * Liefert das passende DataObject zum übergebenen Objektnamen zurück.
     * Wird kein DataObject gefunden, ist der Rückgabewert null.
     * Benutzt lazyLoadPermissions
     *
     * @param string $objectName
     * @return Vpfw_DataObject_RbacRole
     */
    public function getPermission($objectName) {
        $this->lazyLoadPermissions();
        if (true == array_key_exists($objectName, $this->permissionsByName)) {
            return $this->permissionsByName[$objectName];
        } else {
            return null;
        }
    }

    /**
     * Liefert alle RbacPermissions diese DataObject zurück
     * Benutzt lazyLoadPermissions
     */
    public function getPermissions() {
        $this->lazyLoadPermissions();
        return $this->permissions->getArray();
    }

    /**
     * Wenn die RbacPermissions zu diesem DataObject noch nicht geladen wurden,
     * werden sie hier geladen und dem Objekt über die addPermission Methode
     * hinzugefügt.
     */
    private function lazyLoadPermissions() {
        if (false == $this->permissionsWereLoaded) {
            self::checkDataObjectForId($this);
            $permissions = $this->permissionMapper->getEntriesByFieldValue(array('i|RoleId|' . $this->getId()));
            foreach ($permissions as $permission) {
                /* @var $permission Vpfw_DataObject_RbacPermission */
                $this->addPermission($permission);
            }
            $this->permissionsWereLoaded = true;
        }
    }
}
