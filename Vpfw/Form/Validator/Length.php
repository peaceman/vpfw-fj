<?php
class Vpfw_Form_Validator_Length implements Vpfw_Form_Validator_Interface {
    private $minLength;
    private $maxLength;

    public function __construct($minLength, $maxLength = null) {
        $this->minLength = is_null($minLength) ? null : (int)$minLength;
        $this->maxLength = is_null($maxLength) ? null : (int)$maxLength;
    }
    public function run($value) {
        $valueLen = strlen($value);
        if (false == is_null($this->minLength)) {
            if ($this->minLength > $valueLen) {
                return 'Muss mindestens ' . $this->minLength . ' Zeichen Lang sein';
            }
        }
        if (false == is_null($this->maxLength)) {
            if ($this->maxLength < $valueLen) {
                return 'Darf maximal ' . $this->maxLength . ' Zeichen Lang sein';
            }
        }
        return true;
    }
}