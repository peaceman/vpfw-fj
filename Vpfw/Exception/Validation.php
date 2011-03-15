<?php
class Vpfw_Exception_Validation extends Vpfw_Exception_Abstract {
    private $errors = array();

    public function __construct($message, $errors = array()) {
        parent::__construct($message);
    }

    public function getErrors() {
        return $this->errors;
    }
}
