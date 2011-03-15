<?php
class App_Controller_Front_Explicit extends Vpfw_Controller_Front_Abstract {
    public function dispatch(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response, Vpfw_Auth_Session $session) {
        $this->layout->addChildController('navigation', array('navigation', 'index'));
        parent::dispatch($request, $response, $session);
        $response->write($this->layout->renderView());
    }
}
