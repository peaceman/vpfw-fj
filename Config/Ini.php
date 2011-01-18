<?php

class Vpfw_Config_Ini extends Vpfw_Config_Abstract {
    protected function parseFile() {
        parent::parseFile();
        // Wir benutzen eine PHP- Funktion um die Ini- Datei in den
        // Config- Array zu prügeln
        $this->configArray = parse_ini_file($this->fileName, true);
    }

    protected function writeBack() {
        $fileHandle = parent::writeBack();
        $iniStr = '';
        
        // Wir gehen alle sections durch
        foreach ($this->configArray as $sectionName => $values) {
            // Der Konfigurationsarray muss Sections (arrays enhalten) sonst ist er invalid!
            if (!is_array($values))
                throw new Vpfw_Exception_Logical('Config array have to contain sections!');

            $iniStr .= '['.$sectionName.']' . PHP_EOL;

            // Wir gehen nun alle Werte der Section durch und
            // schreiben sie in die Konfigurationsdatei
            foreach ($values as $keyName => $value) {
                if (!is_null($value)) {
                    if (is_bool($value) || is_int($value))
                        $iniStr .= $keyName . ' = ' . $value . PHP_EOL;
                    else if(is_string($value))
                        $iniStr .= $keyName . ' = "' . $value . '"' . PHP_EOL;
                    else
                        throw new Vpfw_Exception_InvalidArgument('Configuration type does not support datatype!');
                }
            }
        }
        fwrite($fileHandle, $iniStr);
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }
}

?>