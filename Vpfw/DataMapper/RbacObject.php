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
}
