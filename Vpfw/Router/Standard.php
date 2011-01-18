<?php
class Vpfw_Router_Standard implements Vpfw_Router_Interface {
    /**
     *
     * @param Vpfw_Request_Interface $request
     * @return Vpfw_Controller_Action_Interface
     */
    public function getActionController(Vpfw_Request_Interface $request) {
        $classIsOk = false;
        if (true == $request->issetParameter('c0n')) {
            $actionController = ucfirst(strtolower($request->getParameter('c0n')));
            $className = 'App_Controller_Action_' . $actionController;
            if (true == class_exists($className)) {
                $classIsOk = true;
            }
        }

        if (false == $classIsOk) {
            $actionController = 'Index';
        }

        if (true == $request->issetParameter('4c7')) {
            $methodName = strtolower($request->getParameter('4c7'));
        } else {
            $methodName = 'index';
        }
        return Vpfw_Factory::getActionController($actionController, $methodName);
    }
}
