<?php
class Vpfw_Rbac_Role {
    /**
     * @var Vpfw_DataObject_RbacRole
     */
    private $roleDao;

    /**
     * @var Vpfw_DataMapper_RbacObject
     */
    private $objectMapper;

    /**
     * @var Vpfw_DataMapper_RbacPermission
     */
    private $permissionMapper;

    public function __construct(Vpfw_DataObject_RbacRole $roleDao, Vpfw_DataMapper_RbacObject $objectMapper, Vpfw_DataMapper_RbacPermission $permissionMapper) {
        $this->roleDao = $roleDao;
        $this->objectMapper = $objectMapper;
        $this->permissionMapper = $permissionMapper;
    }
    
    /**
     * Ermittelt ob diese Rolle Zugriff auf das übergebene Objekt hat.
     *
     * @param string $objectName
     * @return bool
     */
    public function hasAccessTo($objectName) {
        $objectDao = null;
        if (true == is_object($objectName)) {
            if ($objectName instanceof Vpfw_DataObject_RbacObject) {
                $objectDao = $objectName;
                $objectName = $objectDao->getName();
            } else {
                throw new Vpfw_Exception_InvalidArgument('Wenn der Methode Vpfw_Rbac_Role::hasAccessTo statt einem String ein Objekt übergeben wird, muss es sich dabei um ein DataObject des Typs RbacObject handeln.');
            }
        }
        $permissionDao = $this->roleDao->getPermission($objectName);
        if (true == is_null($permissionDao)) {
            return null;
        } else {
            return $permissionDao->getState();
        }
    }

    /**
     *
     * @param string $objectName
     * @return Vpfw_Rbac_Role
     */
    public function grantAccessTo($objectName) {
        $objectDao = null;
        if (true == is_object($objectName)) {
            if ($objectName instanceof Vpfw_DataObject_RbacObject) {
                $objectDao = $objectName;
                $objectName = $objectDao->getName();
            } else {
                throw new Vpfw_Exception_InvalidArgument('Wenn der Methode Vpfw_Rbac_Role::grantAccessTo statt einem String ein Objekt übergeben wird, muss es sich dabei um ein DataObject des Typs RbacObject handeln.');
            }
        } else {
            $objectDao = $this->objectMapper->getEntryByName($objectName);
        }
        $this->addPermission($objectDao->getId(), true);
        return $this;
    }

    /**
     *
     * @param string $objectName
     * @return Vpfw_Rbac_Role
     */
    public function denyAccessTo($objectName) {
        $objectDao = null;
        if (true == is_object($objectName)) {
            if ($objectName instanceof Vpfw_DataObject_RbacObject) {
                $objectDao = $objectName;
                $objectName = $objectDao->getName();
            } else {
                throw new Vpfw_Exception_InvalidArgument('Wenn der Methode Vpfw_Rbac_Role::denyAccessTo statt einem String ein Object übergeben wird, muss es sich dabei um ein DataObject des Typs RbacObject handeln.');
            }
        } else {
            $objectDao = $this->objectMapper->getEntryByName($objectName);
        }
        $this->addPermission($objectDao->getId, false);
        return $this;
    }

    /**
     * Wenn noch keine Permission für diese Rolle und der übergebenen ObjectId
     * besteht wird diese mit einem force insert erstellt und der Rolle direkt
     * zugewiesen.
     * 
     * @param Vpfw_DataObject_RbacObject $rbacObject
     * @param bool $state
     */
    private function addPermission($rbacObject, $state) {
        $permissionDao = $this->roleDao->getPermission($rbacObject->getName());
        // Es existiert keine Rollenspezifische Permission
        if (false == is_null($permissionDao)) {
            $dataArray = array(
                'RoleId' => $this->roleDao->getId(),
                'ObjectId' => $objectId,
                'State' => $state
            );
            // force insert der permission
            $permissionDao = $this->permissionMapper->createEntry($dataArray, true);
            $this->roleDao->addPermission($permissionDao);
        } else {
            $permissionDao->setState($state);
        }
    }
}