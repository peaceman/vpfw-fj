<?php
class Vpfw_Auth_Storage_Session extends Vpfw_Auth_Storage_Abstract {
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function get($name) {
        if (true == array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        } else {
            return null;
        }
    }

    public function __construct($sessionName) {
        session_name($sessionName);
        session_start();
    }
}
