<?php
class Vpfw_Validator_RbacObject {
    /**
     * @var Vpfw_DataMapper_RbacObject
     */
    private $objectMapper;

    /**
     * @param Vpfw_DataMapper_RbacObject
     */
    public function __construct(Vpfw_DataMapper_RbacObject $objectMapper) {
        $this->objectMapper = $objectMapper;
    }

    /**
     *
     * @param string $name
     */
    public function validateName($name) {
        $nameLen = strlen($name);
        if (2 > $nameLen || 32 < $nameLen) {
            throw new Vpfw_Exception_Validation('Der Name eines RbacObjects muss mindestes 2 und maximal 32 Zeichen lang sein');
        }

        if (true == $this->objectMapper->entryWithFieldValuesExists(array('s|Name|' . $name))) {
            throw new Vpfw_Exception_Validation('Es existiert bereits ein RbacObject mit dem Namen ' . $name);
        }
    }
}