<?php
interface Vpfw_Controller_Front_Interface {
    /**
     * @param Vpfw_Request_Interface $request
     * @param Vpfw_Response_Interface $response
     * @param Vpfw_Auth_Session $session
     */
    public function dispatch(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response, Vpfw_Auth_Session $session);
}