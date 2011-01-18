<?php
interface Vpfw_Controller_Action_Interface {
    public function setActionName($name);
    public function setView(Vpfw_View_Interface $view);
    public function execute(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response);
    /**
     * @return Vpfw_View_Interface
     */
    public function getView();
    /**
     * @return string
     */
    public function getActionName();
    /**
     * @param string $placeHolderName
     * @param mixed $ctrlInfo Array aus controllerName und actionName oder der ActionController als Objekt
     */
    public function addChildController($placeHolderName, $ctrlInfo);
}
