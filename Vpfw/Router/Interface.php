<?php
/**
 * Der Router ist dafür zuständig aus den Paramtern des Request-Objektes den
 * Namen des ActionControllers und den Namen der auszuführenden Methode zu
 * extrahieren.
 */
interface Vpfw_Router_Interface {
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
    public function getActionControllerInfo(Vpfw_Request_Interface $request);
}