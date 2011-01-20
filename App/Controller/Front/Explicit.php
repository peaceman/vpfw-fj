<?php
class App_Controller_Front_Explicit extends Vpfw_Controller_Front_Abstract {
    public function dispatch(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response) {
        parent::dispatch($request, $response);
        $response->write($this->layout->renderView());
        $response->flush();
    }
}
