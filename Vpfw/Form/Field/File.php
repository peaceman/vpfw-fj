<?php
class Vpfw_Form_Field_File extends Vpfw_Form_Field {
    public function setValue($value) {
        if (true == array_key_exists($this->getName(), $_FILES)) {
            $this->value = $_FILES[$this->getName()];
        }
    }

    public function isFilled() {
        if (true == empty($this->value['name']) || true == empty($this->value['tmp_name'])) {
            return false;
        } else {
            return true;
        }
    }
}
