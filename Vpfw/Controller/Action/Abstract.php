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
     * @var Vpfw_Auth_Session
     */
    protected $session;

    /**
     * Die Schlüssel dieses Arrays sind die Platzhalter im Template
     * @var array Array aus Namen der ActionController
     */
    protected $childControllers = array();

    protected $isExecuted = false;

    protected $renderView = true;

    public function disableViewRendering() {
        $this->renderView = false;
    }

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
     * Zu beachten ist, dass die ChildController dieses ActionControllers auch
     * die speziellen Objekte von ihm bekommen werden.
     *
     * @param array $environment
     */
    public function __construct($environment = null) {
        if (!is_null($environment)) {
            if (!is_array($environment)) {
                throw new Vpfw_Exception_Logical('The environment variable for ActionController has to be null or an array');
            }
            foreach ($environment as $envName => $envValue) {
                switch ($envName) {
                    case 'request':
                        if ($envValue instanceof Vpfw_Request_Interface)
                            $this->request = $envValue;
                        else
                            throw new Vpfw_Exception_Logical('The environment variable "request" for an ActionController must be an object that implements Vpfw_Request_Interface');
                        break;
                    case 'response':
                        if ($envValue instanceof Vpfw_Response_Interface)
                            $this->response = $envValue;
                        else
                            throw new Vpfw_Exception_Logical('The environment variable "response" for an ActionController must be an object that implements Vpfw_Response_Interface');
                        break;
                    case 'session':
                        if ($envValue instanceof Vpfw_Auth_Session)
                            $this->session = $envValue;
                        else
                            throw new Vpfw_Exception_Logical('The environment variable "session" for an ActionController must be an object from type Vpfw_Auth_Session');
                        break;
                    default:
                        //TODO implement logging if an unknown variable arises
                }
            }
        }
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
     * @param Vpfw_Auth_Session $session
     */
    public function execute(Vpfw_Request_Interface $request, Vpfw_Response_Interface $response, Vpfw_Auth_Session $session) {
        if (true == $this->isExecuted) {
            return;
        }
        $this->request = is_null($this->request) ? $request : $this->request;
        $this->response = is_null($this->response) ? $response : $this->response;
        $this->session = is_null($this->session) ? $session : $this->session;
        $this->{$this->actionToExecute}();
        foreach ($this->childControllers as $placeHolderName => &$controller) {
            if (true == is_array($controller)) {
                list($controllerName, $actionName) = $controller;
                $controller = Vpfw_Factory::getActionController($controllerName, $actionName);
            }
            $controller->execute($request, $response, $session);
        }
        $this->isExecuted = true;
    }

    /**
     *
     * @return string
     */
    public function renderView() {
        if ($this->renderView) {
            foreach ($this->childControllers as $placeHolderName => $controller) {
                if (false == is_object($controller)) {
                    throw new Vpfw_Exception_Logical('Da die renderView Methode erst nach der execute Methode aufgerufen werden darf, sind eigentlich schon alle ActionController-Informationen dazu genutzt worden, die Objekte zu erzeugen.');
                }
                $this->view->setVar($placeHolderName, $controller->renderView());
            }
            return $this->getView()->render();
        } else {
            return;
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
