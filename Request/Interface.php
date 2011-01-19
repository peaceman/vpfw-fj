<?php
interface Vpfw_Request_Interface {
    /**
     * Erstellt ein Array aus allen gespeicherten Parameternamen
     * und gibt dieses zurück.
     *
     * @return array
     */
    public function getParameterNames();

    /**
     * Prüft ob im Request-Objekt ein bestimmter Parameter gesetzt ist.
     *
     * @param string $name
     * @return bool
     */
    public function issetParameter($name);

    /**
     * Gibt den Wert eine Parameters zurück
     *
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Löscht alle gespeicherten Parameter aus dem Request-Objekt
     *
     * @return Vpfw_Request_Interface
     */
    public function clearParameters();

    /**
     * Ermöglicht es eine Liste von Paramtern zu setzen.
     * Paramter die nicht in dieser Liste auftauchen, zuvor aber gesetzt waren
     * werden nicht beeinflusst.
     *
     * @param array $parameters
     * @return Vpfw_Request_Interface
     */
    public function setParameters(array $parameters);

    /**
     * Ermöglicht es einen einzelnen Paramterwert im Request-Objekt
     * zu verändern.
     *
     * @param string $name
     * @param mixed $value
     * @return Vpfw_Request_Interface
     */
    public function setParameter($name, $value);

    /**
     * @return string
     */
    public function getHeader($name);

    /**
     * @return string
     */
    public function getRemoteAddress();

    /**
     * Nimmt ein Array aus Schlüsselnamen entgegen und prüft ob im
     * Array für jeden Schlüsselnamen auch ein Wert existiert.
     *
     * @return bool
     */
    public function areParametersSet(array $names);

    /**
     * Gibt Information darüber, ob der dispatch Prozess für dieses
     * Request-Objekt schon vollständig abgeschlossen ist.
     *
     * @return bool
     */
    public function isDispatched();

    /**
     * Kann zum Beispiel benutzt werden, wenn man den dispatch-Prozess abbrechen
     * möchte obwohl noch ActionController zur Verarbeitung in der Warte-
     * schlange sind.
     * 
     * @param bool $state
     * @return Vpfw_Request_Interface
     */
    public function setDispatchState($state);

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
    public function getNextActionControllerInfo();

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
    public function addActionControllerInfo(array $info);

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
    public function addActionControllerInfos(array $infos);
}
