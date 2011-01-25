<?php
abstract class Vpfw_DataObject_Abstract implements Vpfw_DataObject_Interface, Vpfw_Interface_Observable {
    /**
     * Beinhaltet den Inhalt des Objektes. dieses Array muss von den Kindklassen
     * mit Schlüsseln befüllt werden, da nur A
     * @var array
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $sthChanged = false;

    /**
     * Dieses Array speichert den Status der Objekte, die via lazyloading
     * nachgeladen werden können.
     */
    protected $lazyLoadState;

    /**
     * @var array Beinhaltet die ObserverArrays in denen dieses Objekt referenziert ist.
     */
    private $observers = array();

    public function attachObserver(Vpfw_Interface_Observer $observer) {
        if (false === array_search($observer, $this->observers, true)) {
            $this->observers[] = $observer;
        }
    }

    public function detachObserver(Vpfw_Interface_Observer $observer) {
        $key = array_search($observer, $this->observers, true);
        if (false !== $key) {
            unset($this->observers[$key]);
        }
    }

    public function notifyObserver() {
        foreach ($this->observers as $observer) {
            $observer->observableUpdate($this);
        }
    }
    
    /**
     * @return bool
     */
    public function isSomethingChanged() {
        return $this->sthChanged;
    }

    /**
     * @param bool $state
     */
    public function setSomethingChanged($state) {
        $this->sthChanged = $state;
    }
    
    public function __construct($properties) {
        if (true == is_array($properties)) {
            $this->initialFill($properties);
        }
    }
    
    /**
     * @throws Vpfw_Exception_Logical
     * @param int $which
     * @return array
     */
    public function exportData($which = Vpfw_DataObject_Interface::WITHOUT_ID) {
        $returnArray = array();
        foreach ($this->data as $key => $details) {
            if (false == is_null($details['val'])) {
                switch ($which) {
                    case Vpfw_DataObject_Interface::WITHOUT_ID:
                        if ('Id' != $key) {
                            $returnArray[$key] = $details['val'];
                        }
                        break;
                    case Vpfw_DataObject_Interface::ALL:
                        $returnArray[$key] = $details['val'];
                        break;
                    case Vpfw_DataObject_Interface::CHANGED:
                        if (true == $details['changed']) {
                            $returnArray[$key] = $details['val'];
                        }
                        break;
                    default:
                        throw new Vpfw_Exception_Logical('Keine Ahnung was du von mir willst!?');
                        break;
                }
            }
        }
        return $returnArray;
    }

    /**
     * @throws Vpfw_Exception_Logical
     * @param string $name
     * @return mixed
     */
    protected function getData($name) {
        $this->checkDataKey($name);
        return $this->data[$name]['val'];
    }

    /**
     * @throws Vpfw_Exception_Logical
     * @param string $name
     * @param string $value
     * @param bool $setChangeFlag
     */
    protected function setData($name, $value, $setChangeFlag = true) {
        $this->checkDataKey($name);
        $this->data[$name]['val'] = $value;
        if (true == $setChangeFlag) {
            $this->data[$name]['changed'] = true;
            $this->setSomethingChanged(true);
        }
    }

    /**
     * @throws Vpfw_Exception_Logical
     * @param string $keyName
     */
    private function checkDataKey($keyName) {
        if (false == array_key_exists($keyName, $this->data)) {
            throw new Vpfw_Exception_Logical('Ein Attribut mit dem Namen ' . $keyName . ' ist in diesem DataObject nicht bekannt');
        }
        if (false == array_keys_exists(array('val', 'changed'), $this->data[$keyName])) {
            throw new Vpfw_Exception_Logical('Das data Array in ' . get_called_class() . ' besitzt nicht die erwartete Struktur');
        }
    }

    /**
     * @throws Vpfw_Exception_Logical
     * @param int $id
     * @param bool $validate
     */
    public function setId($id, $validate = true) {
        if ($this->getId() != $id) {
            if (true == $validate) {
                throw new Vpfw_Exception_Logical('Die Id eines DataObjects darf nicht manuell gesetzt werden');
            } else {
                $this->setData('Id', $id);
            }
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->getData('Id');
    }
    /**
     * Befüllt das Objekt mit den übergebenen Daten, welche jedoch nicht
     * validiert werden. Wird im Konstruktor des Objektes aufgerufen.
     * 
     * @throws Vpfw_Exception_Logical
     * @param array $properties 
     */
    private function initialFill(array $properties) {
        foreach ($properties as $propName => $propValue) {
            $this->setData($propName, $propValue, false);
        }
    }

    /**
     * Diese Methode ist dafür vorgesehen, die übergebenen Werte zu validieren
     * und sie als Eigenschaften des Objektes zu setzen.
     * 
     * @throws Vpfw_Exception_Logical
     * @param array $properties Enthält die zu setzenden Werte z.B. array('Id' => 5, 'Name' => 'rolf')
     * @param bool $validate Sollen die Werte aus $properties validiert werden oder nicht
     * @return mixed Entweder true oder die geworfenen Validierungsexceptions als Array, wobei das Feld bei dem der Validierungsfehler aufgetreten ist als Schlüssel benutzt wird.
     */
    public function publicate(array $properties, $validate = true) {
        $validationErrors = array();
        foreach ($properties as $propName => $propValue) {
            $methodName = 'set' . $propName;
            if (false == method_exists($this, $methodName)) {
                throw new Vpfw_Exception_Logical('Ungültiger Wert ' . $propName . ' für ein DataObject des Typs ' . get_called_class());
            }
            if (true == $validate) {
                try {
                    $this->$methodName($propValue);
                } catch (Vpfw_Exception_Validation $e) {
                    $validationErrors[$propName] = $e;
                }
            } else {
                $this->$methodName($propValue, false);
            }
        }
        if (0 == count($validationErrors)) {
            return true;
        } else {
            return $validationErrors;
        }
    }

    public static function checkDataObjectForId(Vpfw_DataObject_Interface $dataObject) {
        if (true == is_null($dataObject->getId())) {
            throw new Vpfw_Exception_Logical('Ein DataObject, das einem anderen DataObject als Eigenschaft hinzugefügt wird, muss auf jeden fall eine Id besitzen.');
        }
    }
}
