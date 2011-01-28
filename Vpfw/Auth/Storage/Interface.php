<?php
interface Vpfw_Auth_Storage_Interface {
    /**
     * @param string $name
     * @param mixed $value
     * @return Vpfw_Auth_Storage_Interface
     */
    public function set($name, $value);

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);
}