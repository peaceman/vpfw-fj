<?php
class Vpfw_Form_Field_File extends Vpfw_Form_Field {
    private $fileValidator;

    public function setValue($value) {
        if (true == array_key_exists($this->getName(), $_FILES)) {
            $this->value = $_FILES[$this->getName()];
        }
    }

    public function isFilled() {
        return is_array($this->value);
    }

    public function  __construct($name, $required = true) {
        $this->fileValidator = new Vpfw_Form_Validator_GenericFile();
        parent::__construct($name, $required);
    }

    public function executeValidators() {
        $result = parent::executeValidators();
        $validationResult = $this->fileValidator->run($this->value);

        if (true === $result && true === $validationResult) {
            return true;
        } else {
            if (true === $result) {
                return array($validationResult);
            } elseif (true === $validationResult) {
                return $result;
            } else {
                return array_merge($result, $validationResult);
            }
        }
    }
}
