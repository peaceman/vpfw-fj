<?php
interface Vpfw_Router_Interface {
    /**
     * @return Vpfw_Controller_Action_Interface
     */
    public function getActionController(Vpfw_Request_Interface $request);

    /**
     * @param Vpfw_Controller_Action_Abstract
     */
    public function setActionController(Vpfw_Controller_Action_Abstract $actionController);
}