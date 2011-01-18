<?php
class Vpfw_Cleaner {
    private static $toClean = array();
    public static function cleanMePls(Vpfw_Interface_Cleaner $object) {
        self::$toClean[] = $object;
    }
    public static function work() {
        $objectCounter = count(self::$toClean);
        while (0 != $objectCounter) {
            $actObject = array_pop(self::$toClean);
            /*
             * Sollte das aktuelle Objekt vom Typ Vpfw_Log_Abstract sein, 
             * setzen wir es an den Anfang des Arrays. Womit wir erreichen,
             * dass der Logger als letztes abgearbeitet wird.
             */
            if ($actObject instanceof Vpfw_Log_Abstract && $objectCounter != 1) {
                array_unshift(self::$toClean, $actObject);
            } else {
                $actObject->clean();
                $objectCounter--;
            }
        }
    }
}
