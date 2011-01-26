<?php
class Vpfw_Form_Validator_NotEmpty implements Vpfw_Form_Validator_Interface {
    public function run($value) {
        if ('' == $value) {
            return 'Darf nicht leer sein';
        } else {
            return true;
        }
    }
}