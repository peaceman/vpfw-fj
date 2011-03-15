<?php
class Vpfw_Form_Field_Radio extends Vpfw_Form_Field {
    private $options;

    public function __construct($name, array $options, $required = true) {
        foreach ($options as $option) {
            $this->options[$option] = false;
        }
        parent::__construct($name, $required);
    }

    public function getRadioValueForOption($option) {
        if (array_key_exists($option, $this->options) && $this->options[$option] === true) {
            return 'checked="checked"';
        } else {
            return null;
        }
    }

    public function setValue($value) {
        $this->options[$value] = true;
        $this->value = $value;
        return $this;
    }
}