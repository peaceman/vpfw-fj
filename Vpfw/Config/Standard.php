<?php
class Vpfw_Config_Standard extends Vpfw_Config_Abstract {
    protected function parseFile() {
        // Prüfen der Dateiverfügbarkeit
        parent::parseFile();
        $fileHandle = fopen($this->fileName, 'r');
        if (false == $fileHandle) {
            // Sollte eigentlich nicht vorkommen, da wir die Dateirechte schon überprüft haben
            throw new Vpfw_Exception_Critical('Die Konfigurationsdatei konnte zum Lesen nicht geöffnet werden');
        }
        flock($fileHandle, LOCK_SH);
        $errorMessages = array();
        $lineCounter = 0;
        while (false == feof($fileHandle)) {
            $lineCounter++;
            $actualLine = trim(fgets($fileHandle));
            if (false == empty($actualLine)) {
                $matches = array();
                $matchCounter = preg_match('/^[\s]*([^\s#]+)[\s]*=[\s]*(?:"([^"]+)"|([^\s]+)).*/', $actualLine, $matches);
                if (0 == $matchCounter) {
                    // Überspringe die aktuelle Zeile, es kann sich um einen Kommentar
                    // oder eine falsche Syntax handeln
                    if ($actualLine[0] != '#') {
                        // Die aktuelle Zeile ist keine Kommentarzeile
                        // Wir speichern hier die Errormeldung in einem Array, damit sie dann
                        // gesammelt mit einer Exception geworfen werden können. Bringt den Vorteil,
                        // dass der User gleich auf alle Syntaxfehler hingewiesen wird.
                        $errorMessages[] = 'Syntaxfehler in Zeile ' . $lineCounter . ' der Konfiguration';
                    }
                } else {
                    // $matches[0] Die ganze Zeile
                    // $matches[1] Der Schlüsselname
                    // $matches[2] Der Wert wenn er in Anführungszeichen gestanden hat
                    // $matches[3] Der Wert wenn er nicht in Anführungszeichen gestanden hat
                    $arrayReference = &$this->configArray;
                    // $arrayReference in die richtige Ebene des Arrays bringen
                    if (false !== strpos($matches[1], '.')) {
                        $keys = explode('.', $matches[1]);
                        foreach ($keys as $key) {
                            $arrayReference = &$arrayReference[$key];
                        }
                    } else {
                        $arrayReference = &$this->configArray[$matches[1]];
                    }

                    if (true == isset($matches[3])) {
                        $arrayReference = $matches[3];
                    } else {
                        $arrayReference = $matches[2];
                    }
                }
            }
        }
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
        if (0 != count($errorMessages)) {
            throw new Vpfw_Exception_Critical(implode("\n", $errorMessages));
        }
    }

    protected function writeBack() {
        $fileHandle = parent::writeBack();

        $recursiveFunction = function($array, $str = '') use ($fileHandle, &$recursiveFunction) {
            foreach ($array as $key => $value) {
                if (true == is_array($value)) {
                    $newStr = '';
                    if (false == empty($str)) {
                        $newStr .= $str . '.' . $key;
                    } else {
                        $newStr .= $key;
                    }
                    call_user_func($recursiveFunction, $value, $newStr);
                } else {
                    $toWrite = '';
                    if (true == empty($str)) {
                        $toWrite .= $key;
                    } else {
                        $toWrite .= $str . '.' . $key;
                    }
                    $toWrite .= ' = ';
                    // Wenn im Wert Leerzeichen vorkommen sollten, setze ihn
                    // in Anführungszeichen
                    if (false !== strpos($value, ' ')) {
                        $toWrite .= '"' . $value . '"';
                    } else {
                        $toWrite .= $value;
                    }
                    $toWrite .= PHP_EOL;
                    fwrite($fileHandle, $toWrite);
                }
            }
        };

        $recursiveFunction($this->configArray);
        
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }
}
