<?php
class Vpfw_Form_Validator_Email implements Vpfw_Form_Validator_Interface {
    public function run($value) {
        if (false == filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Geben sie eine gültige Email-Adresse ein';
        }
        return true;
    }
}
