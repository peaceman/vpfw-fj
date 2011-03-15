<?php
class Vpfw_DataMapper_Language extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'ShortName' => 's',
            'Name' => 's',
        );
        $this->tableName = 'language';
    }

    /**
     * @param string $shortName
     * @return bool
     */
    public function languageExists($shortName) {
        return $this->entryWithFieldValuesExists(array('s|ShortName|' . $shortName));
    }

    /**
     * @param string $shortName
     * @return Vpfw_DataObject_Language
     */
    public function getByShortName($shortName) {
        $dao = $this->getEntriesByFieldValue(array('s|ShortName|' . $shortName));
        if (count($dao) != 1) {
            throw new Vpfw_Exception_OutOfRange('Konnte keine Sprache mit der Abkürzung ' . $shortName . ' in der Datenbank finden');
        }
        return $dao[0];
    }
}
