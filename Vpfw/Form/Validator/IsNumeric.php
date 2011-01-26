<?php
class Vpfw_Form_Validator_IsNumeric implements Vpfw_Form_Validator_Interface {
    private $min;
    private $max;

    public function __construct($min = null, $max = null) {
        $this->min = $min;
        $this->max = $max;
    }

    public function run($value) {
        if (false == is_numeric($value)) {
            return 'Es muss sich um eine Zahl handeln';
        }
        if (false == is_null($this->min)) {
            if ($this->min > $value) {
                return 'Muss mindestens ' . $this->min . ' Zeichen lang sein';
            }
        }

        if (false == is_null($this->max)) {
            if ($this->max < $value) {
                return 'Darf maximal ' . $this->max . ' Zeichen lang sein';
            }
        }
        return true;
    }
}
