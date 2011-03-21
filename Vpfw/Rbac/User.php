<?php
class Vpfw_Rbac_User {
    /**
     * Array bestehend aus den Vpfw_Rbac_Role Objekten des Benutzers
     * 
     * @var array
     */
    private $roles = array();

    /**
     * @var Vpfw_DataMapper_RbacObject
     */
    private $objectMapper;

    /**
     *
     * @param App_DataObject_User $user
     */
    public function __construct(Vpfw_DataMapper_RbacObject $objectMapper, Vpfw_Rbac_UserInterface $user = null) {
        $this->objectMapper = $objectMapper;
        if (false == is_null($user)) {
            foreach ($user->getRbacRoles() as $role) {
                /* @var $role Vpfw_DataObject_RbacRole */
                $this->roles[] = Vpfw_Factory::getRbacRole($role);
            }
        }
    }

    /**
     *
     * @param mixed $objectName
     */
    public function hasAccessTo($objectName) {
        $objectDao = null;
        if (true == is_object($objectName)) {
            if ($objectName instanceof Vpfw_DataObject_RbacObject) {
                $objectDao = $objectName;
                $objectName = $objectDao->getName();
            } else {
                throw new Vpfw_Exception_InvalidArgument('Wenn der Methode Vpfw_Rbac_User::hasAccessTo statt einem String ein Objekt übergeben wird, muss es sich dabei um ein DataObject des Typs RbacObject handeln.');
            }
        } else {
            $objectDao = $this->objectMapper->getEntryByName($objectName);
        }
        $accessState = null;
        foreach ($this->roles as $role) {
            /* @var $role Vpfw_Rbac_Role */
            $tempState = $role->hasAccessTo($objectName);
            // false dominiert
            if ($accessState !== false && $tempState !== null) {
                $accessState = $tempState;
            }
        }
        /*
         * Wenn die Rollen des Benutzers keine Permission zu diesem Objekt
         * definieren wird der Standard des Objektes genommen.
         */
        if (true == is_null($accessState)) {
            $accessState = $objectDao->getDefault();
        }
        return $accessState;
    }
}