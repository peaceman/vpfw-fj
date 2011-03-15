<?php
/**
 * @todo Man sollte diese Abstrakte Klasse zu einem Interface machen
 * Klassen die von dieser Klasse erben, erhalten die Möglichkleit des
 * einfach loggens von Fehlermeldungen und sonstigem.
 */
abstract class Vpfw_Abstract_Loggable {
    /**
     *
     * @var Vpfw_Log_Abstract
     */
    private $logObject;

    /**
     *
     * @var string
     */
    protected $logGroup;

    /**
     *
     * @param Vpfw_Log_Abstract $logObject 
     */
    public function setLogObject(Vpfw_Log_Abstract $logObject) {
        $this->logObject = $logObject;
    }

    /**
     *
     * @param string $msg
     */
    protected function log($msg, $logGroup = null) {
        if (isset($this->logObject)) {
            if (true == is_null($logGroup)) {
                $this->logObject->write($this->logGroup, $msg);
            } else {
                $this->logObject->write($logGroup, $msg);
            }
        }
    }

    public function __construct(Vpfw_Log_Abstract $logObject = null) {
        if (false == is_null($logObject)) {
            $this->setLogObject($logObject);
        }
    }
}
