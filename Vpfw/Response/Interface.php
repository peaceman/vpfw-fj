<?php
interface Vpfw_Response_Interface {
    public function setStatus($status);
    public function addHeader($name, $value);
    public function write($data);
    public function flush();
    public function getBody();
    public function setBody($body);
}
