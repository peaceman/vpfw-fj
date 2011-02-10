<?php
class Vpfw_DataMapper_Translation extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'LanguageId' => 'i',
            'LanguageVariable' => 's',
            'Text' => 's',
        );
        $this->tableName = 'translation';
    }

    public function getByLanguageId($id) {
        return $this->getEntriesByFieldValue(array('i|LanguageId|' . $id));
    }
}
