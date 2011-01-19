<?php
abstract class Vpfw_Request_Abstract implements Vpfw_Request_Interface {
    /**
     * @var array
     */
    private $parameters = array();

    /**
     * Array aus Arrays, welche Informationen zu den als nächstes auszu-
     * führenden ActionControllern enthalten.
     *
     * Dieses Array wird als Queue eingesetzt.
     *
     * @var array
     */
    protected $actionControllerInfos = array();

    /**
     * @var bool
     */
    private $dispatchState = false;

    /**
     * Prüft ob eventuell durch die setDispatchState Methode der dispatchState
     * auf true gesetzt wurde, wenn nicht wird geprüft ob sich in der
     * actionControllerInfos Queue noch Einträge befinden.
     *
     * @return bool
     */
    public function isDispatched() {
        if (true === $this->dispatchState) {
            return true;
        }

        if (0 != count($this->actionControllerInfos)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Ermöglicht es einen einzelnen Paramterwert im Request-Objekt
     * zu verändern.
     *
     * @param string $name
     * @param mixed $value
     * @return Vpfw_Request_Interface
     */
    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Ermöglicht es eine Liste von Paramtern zu setzen.
     * Paramter die nicht in dieser Liste auftauchen, zuvor aber gesetzt waren
     * werden nicht beeinflusst.
     *
     * @param array $parameters
     * @return Vpfw_Request_Interface
     */
    public function setParameters(array $parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /**
     * Löscht alle gespeicherten Parameter aus dem Request-Objekt
     *
     * @return Vpfw_Request_Interface
     */
    public function clearParameters() {
        $this->parameters = array();
        return $this;
    }

    /**
     * Prüft ob im Request-Objekt ein bestimmter Parameter gesetzt ist und
     * auch einen Wert enthält.
     *
     * @param string $name
     * @return bool
     */
    public function issetParameter($name) {
        if (false == isset($this->parameters[$name])) {
            return false;
        }
        if (true == empty($this->parameters[$name])) {
            return false;
        }
        return true;
    }


    /**
     * Nimmt ein Array aus Schlüsselnamen entgegen und prüft ob im
     * Array für jeden Schlüsselnamen auch ein Wert existiert und einen Wert
     * enthält.
     *
     * @return bool
     */
    public function areParametersSet(array $names) {
        foreach ($names as $parameterName) {
            if (false == $this->issetParameter($parameterName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gibt den Wert eine Parameters zurück
     *
     * @return mixed
     */
    public function getParameter($name) {
        if (true == $this->issetParameter($name)) {
            return $this->parameters[$name];
        }
        return null;
    }

    /**
     * Erstellt ein Array aus allen gespeicherten Parameternamen
     * und gibt dieses zurück.
     *
     * @return array
     */
    public function getParameterNames() {
        return array_keys($this->parameters);
    }

    /**
     * Nimmt die Informationen über den auszuführenden ActionController
     * entgegen und speichert sie im Objekt ab. Sollte im Informations-Array
     * kein Wert mit dem Schlüssel MethodName gefunden werden, wird
     * MethodName automatisch auf index gesetzt.
     *
     * Diese Methode wirft eine Vpfw_Exception_Logical Exception wenn im
     * übergebenen Array kein Wert mit dem Schüssel ControllerName zu finden
     * ist.
     *
     * @param array $info
     * @return Vpfw_Request_Interface
     * @throws Vpfw_Exception_Logical
     */
    public function addActionControllerInfo(array $info) {
        if (false == isset($info['ControllerName'])) {
            throw new Vpfw_Exception_Logical('Das ActionController-Informationsarray enthält keinen ControllerName');
        }
        if (false == isset($info['MethodName'])) {
            $info['MethodName'] = 'index';
        }
        $this->actionControllerInfos[] = $info;
        return $this;
    }

    /**
     * Macht im Prinzip das gleiche wie die addActionControllerInfo Methode, mit
     * dem kleinen Unterschied, dass man hier gleich Informationen zu mehreren
     * ActionControllern übergeben kann.
     *
     * Wirft eine Vpfw_Exception_Logical Exception wenn das übergebene
     * Informationsarray nicht aus weitern Arrays besteht.
     *
     * @param array $infos
     * @return Vpfw_Request_Interface
     * @throws Vpfw_Exception_Logical
     */
    public function addActionControllerInfos(array $infos) {
        foreach ($infos as $info) {
            if (false == is_array($info)) {
                throw new Vpfw_Exception_Logical('Das übergebene Infos-Array ist kein Array aus Arrays');
            }
            $this->addActionControllerInfo($info);
        }
        return $this;
    }

    /**
     * Kann zum Beispiel benutzt werden, wenn man den dispatch-Prozess abbrechen
     * möchte obwohl noch ActionController zur Verarbeitung in der Warte-
     * schlange sind.
     *
     * @param bool $state
     * @return Vpfw_Request_Interface
     */
    public function setDispatchState($state) {
        if (false == is_bool($state)) {
            throw new Vpfw_Exception_Logical('In der setDispatchState Methode wird ein boolscher Wert als Übergabeparameter erwartet');
        }
        return $this;
    }

    /**
     * Gibt ein Array, bestehend aus den Namen des ActionControllers
     * und der auszuführenden Methode, zurück.
     * array(
     *     'ControllerName' => 'User',
     *     'MethodName' => 'index',
     * )
     *
     * @return array
     */
    public function getNextActionControllerInfo() {
        $infos = array_shift($this->actionControllerInfos);
        return $infos;
    }
}