<?php
class Vpfw_Request_Http extends Vpfw_Request_Abstract {
    public function __construct() {
        array_walk($_REQUEST, 'trim');
        $this->setParameters($_REQUEST);
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
