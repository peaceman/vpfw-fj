<?php
interface Vpfw_DataObject_Interface {
    const WITHOUT_ID = 0;
    const ALL = 1;
    const CHANGED = 2;
    
    /**
     * Gibt ein Array mit den Daten des DataObjects zurück, wobei die Namen der
     * Werte die Schlüssel sind. Sollte eine Eigenschaft den Wert null haben,
     * wird diese Eigenschaft nicht im Rückgabearray berücksichtigt.
     *
     * @param int $which Definiert welche Daten exportiert werden sollen
     * @return array
     */
    public function exportData($which = Vpfw_DataObject_Interface::WITHOUT_ID);

    /**
     * Setzt den Wert der private Variable $sthChanged
     * @param bool $state
     */
    public function setSomethingChanged($state);

    /**
     *
     * @param int $id
     * @param bool $validate
     */
    public function setId($id, $validate = false);

    /**
     * @return int
     */
    public function getId();
    
    /**
     * @return bool
     */
    public function isSomethingChanged();
}