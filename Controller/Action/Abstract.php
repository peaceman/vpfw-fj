<?php
abstract class Vpfw_Controller_Action_Abstract implements Vpfw_Controller_Action_Interface {
    /**
     * Name der auszuführenden Aktion mit dem Zusatz 'Action'
     * @var string
     */
    protected $actionToExecute;
    
    /**
     *
     * @var Vpfw_Request_Interface
     */
    protected $request;
    
    /**
     *
     * @var Vpfw_Response_Interface
     */
    protected $response;

    /**
     *
     * @var Vpfw_View_Interface
     */
    protected $view;

    /**
     * Die Schlüssel dieses Arrays sind die Platzhalter im Template
     * @var array Array aus Namen der ActionController
     */
    protected $childControllers = array();

    /**
     *
     * @param string $name Name der Aktion ohne den Zusatz 'Action'
     */
    public function setActionName($name) {
        $actionName = $name . 'Action';
        if (false == method_exists($this, $actionName)) {
            $actionName = 'indexAction';
        }
        $this->actionToExecute = $actionName;
    }

    public function getActionName() {
        return str_replace('Action', '', $this->actionToExecute);
    }

    /**
     *
     * @param Vpfw_View_Interface $view
     */
    public function setView(Vpfw_View_Interface $view) {
        $this->view = $view;
    }

    /**
     *
     * @return Vpfw_View_Interface
     */
    public function getView() {
        return $this->view;
    }

    /**
     * Speichert die Objekte $request und $response als Eigenschaften und
     * ruft die auszuführende Methode auf.
     * @param Vpfw_Request_Interface $request
     * @param Vpfw_Response_Interface $response
     */
    public function execute(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response) {
        $this->request = $request;
        $this->response = $response;
        $this->{$this->actionToExecute}();
        foreach ($this->childControllers as $placeHolderName => $controller) {
            if (true == is_array($controller)) {
            list($controllerName, $actionName) = $controller;
                $controller = Vpfw_Factory::getActionController($controllerName, $actionName);
            }
            $controller->execute($request, $response);
            $this->view->setVar($placeHolderName, $controller->getView()->render());
        }
    }

    /**
     * Speichert die ActionController unter dem $placeHolderName in einem Array
     * @param string $placeHolderName Mit diesem Platzhalter kann die Position dieses ActionControllers im Layout bestimmt werden
     * @param Vpfw_Controller_Action_Interface $ctrlInfo
     */
    public function addChildController($placeHolderName, $ctrlInfo) {
        if (true == is_array($ctrlInfo)) {
            if (2 != count($ctrlInfo)) {
                throw new Vpfw_Exception_Logical('Das ctrlInfo Array muss aus 2 Elementen bestehen');
            }
            $this->childControllers[$placeHolderName] = $ctrlInfo;
        } elseif (true == is_object($ctrlInfo)) {
            if (false == $ctrlInfo instanceof Vpfw_Controller_Action_Interface) {
                throw new Vpfw_Exception_Logical('Einem ActionController können nur ActionController als child hinzugefügt werden');
            }
            $this->childControllers[$placeHolderName] = $ctrlInfo;
        } else {
            throw new Vpfw_Exception_Logical('Die addChildController Methode benötigt entweder den ActionController als Objekt oder ein Array, welches den Namen und die Action des ActionControllers beinhaltet');
        }
    }
    
    abstract protected function indexAction();
}
