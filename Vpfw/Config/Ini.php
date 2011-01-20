<?php
class Vpfw_Config_Ini extends Vpfw_Config_Abstract {
    protected function parseFile() {
        parent::parseFile();
        // Wir benutzen eine PHP- Funktion um die Ini- Datei in den
        // Config- Array zu prügeln
        $this->configArray = parse_ini_file($this->fileName, true);
    }

    protected function writeBack() {
        $iniStr = '';
        
        // Wir gehen alle sections durch
        foreach ($this->configArray as $sectionName => $values) {
            // Das Konfigurationsarray muss Sections (arrays enhalten) sonst ist es invalid!
            if (!is_array($values))
                throw new Vpfw_Exception_Logical('Config array has to contain sections!');

            $iniStr .= '[' . $sectionName . ']' . PHP_EOL;

            // Wir gehen nun alle Werte der Section durch und
            // schreiben sie in die Konfigurationsdatei
            foreach ($values as $keyName => $value) {
                if (!is_null($value)) {
                    if (is_int($value))
                        $iniStr .= $keyName . ' = ' . $value . PHP_EOL;
                    elseif (is_bool($value)) {
                        if (false === $value) {
                            $iniStr .= $keyName . ' = 1' . PHP_EOL;
                        } else {
                            $iniStr .= $keyName . ' = 0' . PHP_EOL;
                        }
                    }
                    elseif (is_string($value))
                        $iniStr .= $keyName . ' = "' . $value . '"' . PHP_EOL;
                    elseif (is_array($value))
                        throw new Vpfw_Exception_Logical('Bei einer Konfiguration mit INI-Datei ist es nicht möglich die Konfiguration mit mehr als einer Unterebene zu gestalten.');
                    else
                        throw new Vpfw_Exception_InvalidArgument('Configuration type does not support datatype!');
                }
            }
        }
        $fileHandle = parent::writeBack();
        fwrite($fileHandle, $iniStr);
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }
}
