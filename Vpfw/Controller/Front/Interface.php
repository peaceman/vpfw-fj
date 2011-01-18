<?php
interface Vpfw_Controller_Front_Interface {
    /**
     * @param Vpfw_Request_Interface $request
     * @param Vpfw_Response_Interface $response
     */
    public function handleRequest(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response);
}