<?php
abstract class Vpfw_Config_Abstract extends Vpfw_Abstract_Loggable implements Vpfw_Interface_Cleaner {
    protected $configArray = array();
    private $changedValues = array();
    protected $fileName;


    public function __construct($fileName) {
        Vpfw_Cleaner::cleanMePls($this);
        $this->fileName = $fileName;
        $this->logGroup = 'config';
        $this->parseFile();
    }
    
    /**
     * Wirft eine Vpfw_Exception_InvalidArgument Exception, wenn das
     * angeforderte Element nicht existiert.
     * @param string $name
     * @return mixed
     */
    public function getValue($name) {
        if (false === strpos( $name, '.')) {
            if (false == array_key_exists($name, $this->configArray)) {
                throw new Vpfw_Exception_InvalidArgument('Ein Konfigurationseintrag mit dem Schlüssel "' . $name . '" existiert nicht!');
            } else {
                return $this->configArray[$name];
            }
        } else {
            $targetReference = &$this->configArray;
            $keys = explode('.', $name);
            foreach ($keys as $key) {
                if (false == array_key_exists($key, $targetReference)) {
                    throw new Vpfw_Exception_InvalidArgument('Ein Konfigurationseintrag mit dem Schlüssel "' . $name . '" existiert nicht!');
                } else {
                    $targetReference = &$targetReference[$key];
                }
            }
            return $targetReference;
        }
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function setValue($key, $value) {
        if (false === strpos('.', $key)) {
            $this->configArray[$key] = $value;
        } else {
            $targetReference = &$this->configArray;
            $keys = explode('.', $key);
            foreach ($keys as $key) {
                $targetReference = &$targetReference[$key];
            }
            $targetReference = $value;
        }
        $this->changedValues[$key] = true;
    }

    /**
     * Prüft die Konfigurationsdatei auf Verfügbarkeit
     * 
     */
    protected function parseFile() {
        // Wenn hier Fehler auftreten sollten, wird die Ausführung des Skriptes gestoppt,
        // da die Konfiguration elementar ist.
        if (false == file_exists($this->fileName)) {
            throw new Vpfw_Exception_Critical('Die zu öffnende Konfigurationsdatei (' . $this->fileName . ') existiert nicht');
        }
        if (false == is_readable($this->fileName)) {
            throw new Vpfw_Exception_Critical('Die zu parsende Konfigurationsdatei (' . $this->fileName . ') ist nicht lesbar, überprüfen sie die gesetzten Dateirechte');
        }
    }

    /**
     * Prüft die Datei auf verfügbarkeit, lockt sie und löscht ihren Inhalt anschließend
     * @todo Die Methode der Kindklassen sollte so angepasst werden, dass sie 
     * nur die nötigen Konfigurationswerte ändern und nicht die komplette Datei
     * neu schreiben. Bringt den Vorteil, dass Kommentare in der Konfiguration 
     * erhalten bleiben.
     * @return filehandle
     */
    protected function writeBack() {
        if (false == is_writable($this->fileName)) {
            // Die Konfigurationsdatei ist nicht beschreibbar, versuche die Dateirechte anzupassen
            if (false == chmod($this->fileName, 0644)) {
                throw new Vpfw_Exception_Feature('Das zurückschreiben der geänderten Konfiguration konnte aufgrund von fehlenden Dateirechten nicht durchgeführt werden');
            }
        }
        $fileHandle = fopen($this->fileName, 'a');
        if (false == $fileHandle) {
            // Sollte eigentlich nicht vorkommen, da wir ja vorher schon die Dateirechte
            // überprüft haben, aber man weiß ja nie
            throw new Vpfw_Exception_Feature('Die Konfigurationsdatei (' . $this->fileName . ') konnte zum zurückschreiben nicht geöffnet werden');
        }
        if (false == flock($fileHandle, LOCK_EX)) {
            throw new Vpfw_Exception_Feature('Die Konfigurationsdatei (' . $this->fileName . ') konnte nicht gelockt werden');
        }
        ftruncate($fileHandle, 0);
        return $fileHandle;
    }

    public function clean() {
        try {
            if (0 != count($this->changedValues)) {
                $this->writeBack();
            }
        } catch (Vpfw_Exception_Feature $e) {
            $this->log($e->getMessage());
        }
    }
}
