<?php
class Vpfw_Form_Filter_TrimSpaces implements Vpfw_Form_Filter_Interface {
    public function run($value) {
        return trim($value);
    }
}