<?php
class Vpfw_DataMapper_RbacObject extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'Default' => 'i',
            'Name' => 's',
        );
        $this->tableName = 'rbac_object';
    }

    public function getEntryByName($name) {
        if (false == is_string($name)) {
            throw new Vpfw_Exception_InvalidArgument('Es wird ein String erwartet');
        }
        if (true == empty($name)) {
            throw new Vpfw_Exception_InvalidArgument('Der übergebene String sollte nicht leer sein');
        }
        $result = $this->getEntriesByFieldValue(array('s|Name|' . $name));
        return $result[0];
    }
}
