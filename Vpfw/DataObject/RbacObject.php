<?php
class Vpfw_DataObject_RbacObject extends Vpfw_DataObject_Abstract {
    public function __construct($properties = null) {
        $this->data = array(
            'Id' => null,
            'Default' => null,
            'Name' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => null);
        }
        parent::__construct($properties);
    }

    public function getDefault() {
        if (0 == $this->getData('Default')) {
            return false;
        } else {
            return true;
        }
    }

    public function getName() {
        return $this->getName('Name');
    }

    public function setDefault($state, $validate = true) {
        if (false == is_bool($state)) {
            throw new Vpfw_Exception_InvalidArgument('Die Methode Vpfw_DataObject_RbacObject::setDefault akzeptiert nur boolsche Werte');
        }
        if ($state !== $this->getDefault()) {
            if (true == $state) {
                $this->setData('Default', 1);
            } else {
                $this->setData('Default', 0);
            }
        }
    }

    public function setName($name, $validate = true) {
        if ($this->getName() != $name) {
            if (true == $validate) {
                $this->validator->validateName($name);
            }
            $this->setData('Name', $id);
        }
    }
}
