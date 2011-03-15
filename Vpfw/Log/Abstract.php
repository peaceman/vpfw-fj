<?php
/**
 * @todo implement logging into postgresql and sqlite
 */
abstract class Vpfw_Log_Abstract {
    /**
     * Wenn Nachrichten geloggt werden sollen,
     * deren Loggruppe nicht hier hinterlegt ist,
     * werde die Nachrichten ignoriert.
     * @var array Array aus Strings
     */
    protected $logGroupsToLog;
    protected $configObject;
    protected $logAll;

    /**
     *
     * @param array/string $logGroupsToLog
     */
    public function __construct(Vpfw_Config_Abstract $configObject) {
        $this->configObject = $configObject;
        try {
            $logGroups = array_filter(explode(',', $this->configObject->getValue('Log.Groups')));
            array_walk($logGroups, function(&$value, $key) {
                $value = trim($value);
            });
        } catch (Vpfw_Exception_InvalidArgument $e) {
            // Es existiert kein Konfigurationseintrag, benutze den Standardwert
            $logGroups = array('base');
        }
        $this->logGroupsToLog = singleValueToArray($logGroups);
        if (true == in_array('all', $this->logGroupsToLog)) {
            $this->logAll = true;
        } else {
            $this->logAll = false;
        }
    }
    
    /**
     * Prüft ob die Nachricht geloggt werden soll und ruft dann die _write
     * Methode auf.
     * @param string $logGroup
     * @param string $msg
     */
    public function write($logGroup, $msg) {
        if (true == $this->logAll || true == in_array($logGroup, $this->logGroupsToLog)) {
            $this->_write($logGroup, $msg);
        }
    }

    /**
     * Schreibt die Lognachricht in das entsprechende Speicherziel
     * @param string $logGroup
     * @param string $msg
     */
    abstract protected function _write($logGroup, $msg);
}
