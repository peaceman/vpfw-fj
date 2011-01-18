<?php
class Vpfw_Request_Http implements Vpfw_Request_Interface {
    private $parameters;

    /**
     * Wird auf True gesetzt sobald der aktuelle Dispatchprozess
     * abeschlossen ist.
     * 
     * @var bool 
     */
    private $isHandled;

    /**
     * Array aus dem Namen des als nächstes auszuführenden ActionControllers und
     * dem Namen der entsprechenden Methode.
     *
     * @var array
     */
    private $nextActionController = array(
        'ControllerName' => null,
        'MethodName' => null,
    );

    /**
     * Beauftragt die Vpfw_Factory damit, den definierten ActionController zu
     * erzeugen und diesem mitzuteilen, welche Methode er auszuführen hat.
     *
     * @return Vpfw_Controller_Action_Abstract
     */
    public function getNextActionController() {

    }

    /**
     * @return bool
     */
    public function isHandled() {
        return $this->isHandled;
    }

    /**
     * @param bool $state
     */
    public function setHandleState($state) {
        $this->isHandled = (bool)$state;
    }

    public function __construct() {
        $this->parameters = $_REQUEST;
        array_walk($this->parameters, 'trim');
    }

    public function issetParameter($name) {
        return isset($this->parameters[$name]);
    }

    public function areParametersSet(array $parameterNames) {
        foreach ($parameterNames as $parameterName) {
            if (false == $this->issetParameter($parameterName)) {
                return false;
            }
            $parameter = $this->getParameter($parameterName);
            if (true == empty($parameter)) {
                return false;
            }
        }
        return true;
    }

    public function getParameter($name) {
        if (true == isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        return null;
    }

    public function getParameterNames() {
        return array_keys($this->parameters);
    }

    public function getHeader($name) {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (true == isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        return null;
    }

    public function getRemoteAddress() {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function url($controller, $action = 'index', $parameters = array()) {
        $retUrl = 'index.php?c0n=' . urlencode($controller) . '&4c7=' . urlencode($action);
        foreach ($parameters as $key => $value) {
            $retUrl .= '&' . urlencode($key) . '=' . urlencode($value);
        }
        return $retUrl;
    }
}
