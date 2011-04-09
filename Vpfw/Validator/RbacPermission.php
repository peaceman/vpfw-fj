<?php
class Vpfw_Validator_RbacPermission {
    public function validateState($state) {
        if (!is_bool($state)) {
            throw new Vpfw_Exception_Validation('Der State einer RbacPermission muss einen boolschen Wert haben');
        }
    }
}