<?php
class Vpfw_Validator_RbacRole {
    /**
     * @var Vpfw_DataMapper_RbacRole
     */
    private $roleMapper;

    /**
     * @param Vpfw_DataMapper_RbacRole $roleMapper
     */
    public function __construct(Vpfw_DataMapper_RbacRole $roleMapper) {
        $this->roleMapper = $roleMapper;
    }

    public function validateName($name) {
        $nameLen = strlen($name);
        if (2 > $nameLen || 32 < $nameLen) {
            throw new Vpfw_Exception_Validation('Der Name einer RbacRole muss mindestens 2 Zeichen und maximal 32 Zeichen lang sein');
        }

        if (true == $this->roleMapper->entryWithFieldValuesExists(array('s|Name|' . $name))) {
            throw new Vpfw_Exception_Validation('Es existiert bereits eine RbacRole mit dem Namen ' . $name);
        }
    }
}