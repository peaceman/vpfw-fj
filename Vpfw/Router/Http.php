<?php
class Vpfw_Router_Http implements Vpfw_Router_Interface {
    /**
     * Liefert den Namen des ActionControllers und der auszuführenden
     * Methode. Sollte im Request-Objekt kein Hinweis auf die auszuführende
     * Methode zu finden sein, wird automatisch die index Methode eingetragen.
     *
     * array(
     *     'ControllerName' => 'User',
     *     'MethodName' => 'index',
     * )
     * @return array
     */
    public function getActionControllerInfo(Vpfw_Request_Interface $request) {
        $classExists = false;
        if (true == $request->issetParameter('c0n')) {
            $actionControllerName = ucfirst(strtolower($request->getParameter('c0n')));
            $classExists = class_exists(('App_Controller_Action_' . $actionControllerName));
        }

        if (false == $classExists) {
            $actionControllerName = 'Index';
        }

        if (true == $request->issetParameter('4c7')) {
            $methodName = strtolower($request->getParameter('4c7'));
        } else {
            $methodName = 'index';
        }
        return array('ControllerName' => $actionControllerName, 'MethodName' => $methodName);
    }

    public static function url($controller, $action = 'index', $parameters = array()) {
        $retUrl = 'index.php?c0n=' . urlencode($controller) . '&4c7=' . urlencode($action);
        foreach ($parameters as $key => $value) {
            $retUrl .= '&' . urlencode($key) . '=' . urlencode($value);
        }
        return $retUrl;
    }
}
