<?php
class Vpfw_Log_File extends Vpfw_Log_Abstract {
    private $configArray = array(
        'Name' => 'vpfw.log',
        'Format' => '[%Y-%m-%d/%H:%M:%S] $logGroup | $message',
        'Cached' => 'true',
    );
    private $fileHandle;

    public function __construct(Vpfw_Config_Abstract $configObject) {
        $startupErrors = array();
        parent::__construct($configObject);
        try {
            $this->configArray = array_merge($this->configArray, $this->configObject->getValue('Log.File'));
        } catch(Vpfw_Exception_InvalidArgument $e) {
            $startupErrors[] = $e->getMessage();
        }
        $callbackFunc = function($match) {
            if (false == Vpfw_Log_File::verifyTimePlaceholder($match[1])) {
                return '%' . $match[1];
            } else {
                return $match[1];
            }
        };
        $this->configArray['Format'] = preg_replace_callback('/(%.)/', $callbackFunc, $this->configArray['Format']);
        if (0 != count($this->logGroupsToLog)) {
            $this->fileHandle = fopen($this->configArray['Name'], 'a');
            if (false == $this->fileHandle) {
                throw new Vpfw_Exception_Critical('Konnte die Logdatei (' . $this->configArray['Name'] . ') nicht zum Schreiben öffnen');
            }
            foreach ($startupErrors as $msg) {
                $this->write('base', $msg);
            }
            if ('false' == $this->configArray['Cached']) {
                fclose($this->fileHandle);
            }
        }
    }

    protected function _write($logGroup, $msg) {
        if ('false' == $this->configArray['Cached']) {
            $this->fileHandle = fopen($this->configArray['Name'], 'a');
        }
        $toWrite = strftime($this->configArray['Format']);
        $toWrite = str_replace(array('$logGroup', '$message'), array($logGroup, $msg), $toWrite);
        flock($this->fileHandle, LOCK_EX);
        fwrite($this->fileHandle, $toWrite . PHP_EOL);
        flock($this->fileHandle, LOCK_UN);
        if ('false' == $this->configArray['Cached']) {
            fclose($this->fileHandle);
        }
    }

    public static function verifyTimePlaceholder($ph) {
        $validPlaceholders = array(
            '%a',
            '%A',
            '%b',
            '%c',
            '%C',
            '%d',
            '%D',
            '%e',
            '%g',
            '%G',
            '%h',
            '%H',
            '%I',
            '%j',
            '%m',
            '%M',
            '%n',
            '%p',
            '%r',
            '%R',
            '%S',
            '%t',
            '%T',
            '%u',
            '%U',
            '%V',
            '%w',
            '%W',
            '%x',
            '%X',
            '%y',
            '%Y',
            '%Z',
        );
        return in_array($ph, $validPlaceholders);
    }
}
