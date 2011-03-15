<?php
class Vpfw_Form_Validator_Equals implements Vpfw_Form_Validator_Interface {
    private $toCompareWith;

    public function __construct(Vpfw_Form_Field $value) {
        $this->toCompareWith = $value;
    }

    public function run($value) {
        if ($value != $this->toCompareWith->getValue()) {
            return 'Stimmt nicht mit ' . $this->toCompareWith->getName() . ' überein';
        } else {
            return true;
        }
    }
}