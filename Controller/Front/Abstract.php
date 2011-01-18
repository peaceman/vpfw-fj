<?php
class Vpfw_Controller_Front_Abstract implements Vpfw_Controller_Front_Interface {
    /**
     * @var Vpfw_Router_Interface
     */
    protected $router;

    /**
     * @var Vpfw_Controller_Action_Interface
     */
    protected $layout;

    /**
     * @param Vpfw_Router_Interface $router
     */
    public function __construct(Vpfw_Router_Interface $router) {
        $this->router = $router;
    }

    /**
     * @param Vpwf_Request_Interface $request
     * @param Vpfw_Response_Interface $response
     */
    public function handleRequest(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response) {
        $this->layout = Vpfw_Factory::getActionController('Layout', 'index');
        $actionController = $this->router->getActionController($request);
        $this->layout->addChildController('content', $actionController);
    }
}
