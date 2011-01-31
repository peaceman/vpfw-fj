<?php
class Vpfw_Form_Validator_InArray implements Vpfw_Form_Validator_Interface {
    private $validValues = array();

    public function __construct(array $validValues) {
        $this->validValues = $validValues;
    }

    public function run($value) {
        if (false == in_array($value, $this->validValues)) {
            return 'Darf nur folgende Werte haben ' . implode(', ', $this->validValues);
        } else {
            return true;
        }
    }
}