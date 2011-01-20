<?php
class Vpfw_Config_Ini extends Vpfw_Config_Abstract {
	private $arrayReference = null;

    public function parseFile() {
        parent::parseFile();
		$fileHandle = fopen($this->fileName, 'r');
		if (!$fileHandle) {
			// Sollte eigentlich nicht vorkommen, da wir die Dateirechte schon überprüft haben
			throw new Vpfw_Exception_Critical('Die Konfigurationsdatei konnte zum Lesen nicht geöffnet werden');
		}
		flock($fileHandle, LOCK_SH);
		$errorMessages = array();
		$lineCounter = 0;
		$arrayReference = &$this->configArray;
		while (!feof($fileHandle)) {
			$lineCounter++;
			$actualLine = trim(fgets($fileHandle));
			
			// Wenn die Zeile leer ist oder ein Kommentar eingeleitet wirt mit der nächsten
			// Zeile fortfahren
			if (empty($actualLine))	continue; if ($actualLine[0] == ';' || $actualLine[0] == '#') continue;
			
			// ^\[([^]]*)\]$
			$sectionSearchPattern = '/^\\[([^]]*)\\]$/';
			// ^([^=]*)\s*=\s*(?:([^"].*)|(?:"([^"]*)"))$
			$keyValuePairSearchPattern = '/^([^=]*)\\s*=\\s*(?:([^"].*)|(?:"([^"]*)"))$/';
			$matches = array();
			$matchCount = 0;
			
			$matchCount = preg_match($sectionSearchPattern, $actualLine, $matches);
			if ($matchCount > 0) {
				$this->arrayReference = &$this->configArray[$matches[1]];
				// $matches[0] Die ganze Zeile
				// $matches[1] Name der Section
				continue; // Wir untersuchen die nächste Zeile
			}
			else {
				$matchCount = preg_match($keyValuePairSearchPattern, $actualLine, $matches);
				if ($matchCount > 0)
				{
					// $matches[0] Die ganze Zeile
					// $matches[1] Der Schlüssel
					// $matches[2] Der Wert ohne umschliessende Anführungszeichen
					// $matches[3] Der Wert mit umschliessenden Anführungszeichen
					$key = $matches[1];
					if (!empty($matches[2])) $value = $matches[2]; else $value = $matches[3];
					$keyArr = explode('.', $key);
					if (count($keyArr) < 2)
					{
						$this->arrayReference[$key] = $value;
						continue; // Wir untersuchen die nächste Zeile
					}
					else
					{
						// $arrayReference in die richtige Ebene des Arrays bringen
						// und den entsprechenden wert zuweisen
						foreach ($keyArr as $key) {
							$arrayReference = &$arrayReference[$key];
						}
						$arrayReference = $value;
					}
					
					
					
					continue; // Wir untersuchen die nächste Zeile
				} else 
					$errorMessages[] = 'Line ' . $lineCounter . ' has wrong Syntax in configuration file '.$this->fileName.'!';
			}
		}
		flock($fileHandle, LOCK_UN);
		fclose($fileHandle);
		if (0 != count($errorMessages))
			throw new Vpfw_Exception_Critical(implode("\n", $errorMessages));
			
    }
}
