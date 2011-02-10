<?php
class Vpfw_DataObject_Translation extends Vpfw_DataObject_Abstract {
    public function __construct($properties = null) {
        $this->data = array(
            'Id' => null,
            'LanguageId' => null,
            'LanguageVariable' => null,
            'Text' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        parent::__construct($properties);
    }

    public function getLanguageId($id) {
        if (is_object($this->language)) {
            return $this->language->getId();
        } else {
            return $this->getData('LanguageId');
        }
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguageId($id, $validate = true) {
        if ($this->getLanguageId() != $id) {
            if ($validate) {
                $this->validator->validateLanguageId($id);
            }
            $this->setData('LanguageId', $id);
            $this->setLanguage(null);
        }
        return $this;
    }

    public function setLanguage(Vpfw_DataObject_Language $language) {
        $this->language = $language;
        if (is_object($language)) {
            $this->setData('LanguageId', $language->getId());
        }
    }

    public function setLanguageVariable($languageVariable, $validate = true) {
        if ($this->getLanguageVariable() != $languageVariable) {
            if ($validate) {
                $this->validator->validateLanguageVariable($languageVariable);
            }
            $this->setData('LanguageVariable', $languageVariable);
        }
        return $this;
    }

    public function getLanguageVariable() {
        return $this->getData('LanguageVariable');
    }

    public function setText($text, $validate = true) {
        if ($this->getText() != $text) {
            if ($validate) {
                $this->validator->validateText($text);
            }
            $this->setData('Text', $text);
        }
    }

    public function getText() {
        return $this->getData('Text');
    }
}