<?php
interface Vpfw_Request_Interface {
    public function getParameterNames();
    public function issetParameter($name);
    public function getParameter($name);
    public function getHeader($name);
    public function getRemoteAddress();
}
