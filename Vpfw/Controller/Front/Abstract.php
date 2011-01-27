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
        $this->layout = Vpfw_Factory::getActionController('Layout', 'index');
    }

    /**
     * @param Vpwf_Request_Interface $request
     * @param Vpfw_Response_Interface $response
     */
    public function dispatch(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response) {
        $initialActionControllerInfo = $this->router->getActionControllerInfo($request);
        $request->addActionControllerInfo($initialActionControllerInfo);

        while (false == $request->isDispatched()) {
            $info = $request->getNextActionControllerInfo();
            $contentActionController = Vpfw_Factory::getActionController($info['ControllerName'], $info['MethodName']);
            $contentActionController->execute($request, $response);
        }
        
        $this->layout->addChildController('content', $contentActionController);
        $this->layout->execute($request, $response);
    }
}
