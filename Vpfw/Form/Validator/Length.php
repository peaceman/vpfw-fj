<?php
class Vpfw_Form_Validator_Length implements Vpfw_Form_Validator_Interface {
    private $minLength;
    private $maxLength;

    public function __construct($minLength, $maxLength) {
        $this->minLength = (int)$minLength;
        $this->maxLength = (int)$maxLength;
    }
    public function run($value) {
        $valueLen = strlen($value);
        if ($this->minLength > $valueLen || $this->maxLength < $valueLen) {
            return 'Muss mindestens ' . $this->minLength . ' und maximal ' . $this->maxLength . ' Lang sein';
        }
        return true;
    }
}