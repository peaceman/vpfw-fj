<?php
interface Vpfw_Router_Interface {
    /**
     * @return Vpfw_Controller_Action_Interface
     */
    public function getActionController(Vpfw_Request_Interface $request);
}