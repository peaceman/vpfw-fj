<?php
class Vpfw_Config_Php extends Vpfw_Config_Abstract {
    protected function parseFile() {
        // Prüfen auf Dateiverfügbarkeit
        parent::parseFile();
        // Wollen wir mal hoffen, dass PHP die Dateien beim Includen lockt
        $this->configArray = include $this->fileName;
    }

    protected function writeBack() {
        $fileHandle = parent::writeBack();
        fwrite($fileHandle, '<?php' . PHP_EOL . 'return ' . PHP_EOL . arrayToStr($this->configArray));
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }
}
