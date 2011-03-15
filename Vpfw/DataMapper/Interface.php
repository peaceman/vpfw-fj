<?php
interface Vpfw_DataMapper_Interface {
    public function createEntry($parameters = null, $forceInsert = null);    
    public function entryWithFieldValuesExists(array $fieldValues);
    public function getEntryById($id, $autoLoad = true);
    public function getEntriesByFieldValue(array $fieldValues);
    public function getAllEntries();
    public function deleteEntryById($id);
    public function deleteEntriesByFieldValue(array $fieldValues);
    public function deleteAllEntries();
}
