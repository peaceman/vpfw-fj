<?php
class Vpfw_Factory {
    private static $objectCache = array();
    /**
     *
     * @var Vpfw_Config_Abstract 
     */
    private static $configObject;

    public static function getValidator($type) {
        $className = 'App_Validator_' . $type;
        if (true == isset(self::$objectCache[$className])) {
            return self::$objectCache[$className];
        }

        if (false == class_exists($className)) {
            throw new Vpfw_Exception_Logical('Eine Validator des Typs ' . $type . ' existiert nicht');
        }

        switch ($type) {
            case 'User':
                self::$objectCache[$className] = new App_Validator_User(self::getModel('User'));
                break;
            case 'Location':
                self::$objectCache[$className] = new App_Validator_Location(self::getDataMapper('Location'));
                break;
            case 'Event':
                self::$objectCache[$className] = new App_Validator_Event(self::getDataMapper('Event'), self::getDataMapper('Location'));
                break;
            case 'Project':
                self::$objectCache[$className] = new App_Validator_Project(self::getDataMapper('Project'));
                break;
            case 'Link':
                self::$objectCache[$className] = new App_Validator_Link(self::getDataMapper('Link'));
                break;
            case 'Site';
                self::$objectCache[$className] = new App_Validator_Site(self::getDataMapper('Site'));
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des Validators mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
        }

        return self::$objectCache[$className];
    }

    /**
     *
     * @param string $type
     * @return Vpfw_DataMapper_Interface
     */
    public static function getDataMapper($type) {
        $className = 'App_DataMapper_' . $type;
        if (true == isset(self::$objectCache[$className])) {
            return self::$objectCache[$className];
        }

        if (false == class_exists($className)) {
            throw new Vpfw_Exception_Logical('Ein DataMapper des Typs ' . $type . ' existiert nicht');
        }

        switch ($type) {
            case 'User':
                self::$objectCache[$className] = new App_DataMapper_User(self::getDatabase());
                break;
            case 'Event':
                self::$objectCache[$className] = new App_DataMapper_Event(self::getDatabase());
                break;
            case 'Location':
                self::$objectCache[$className] = new App_DataMapper_Location(self::getDatabase());
                break;
            case 'Project':
                self::$objectCache[$className] = new App_DataMapper_Project(self::getDatabase());
                break;
            case 'Link':
                self::$objectCache[$className] = new App_DataMapper_Link(self::getDatabase());
                break;
            case 'Site':
                self::$objectCache[$className] = new App_DataMapper_Site(self::getDatabase());
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataMappers mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
        return self::$objectCache[$className];
    }

    /**
     * @static
     * @throws Vpfw_Exception_Logical
     * @param  $type
     * @param  $properties
     * @return App_DataObject_Deletion|App_DataObject_Picture|App_DataObject_RuleViolation|App_DataObject_Session|App_DataObject_User
     */
    public static function getDataObject($type, $properties = null) {
        $className = 'App_DataObject_' . $type;
        if (false == class_exists($className)) {
            throw new Vpfw_Exception_Logical('Ein DataObject des Typs ' . $type . ' existiert nicht');
        }

        switch ($type) {
            case 'User':
                return new App_DataObject_User(self::getValidator('User'), $properties);
                break;
            case 'Event':
                $location = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['LocationId'])) {
                        try {
                            $location = self::getDataMapper('Location')->getEntryById($properties['LocationId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $location = self::getDataMapper('Location')->createEntry(array('Id' => $properties['LocationId'], 'Name' => $properties['LocationName']));
                        }
                        /**
                         *  Die Informationen über das fremde DataObject müssen
                         *  hier gelöscht werden, da sonst im Konstruktor des
                         *  eigentlichen DataObjects eine Exception geworfen wird.
                         */
                        unset($properties['LocationId']);
                        unset($properties['LocationName']);
                    }
                }
                $dataObject = new App_DataObject_Event(self::getValidator('Event'), $properties);
                if (false == is_null($location)) {
                    $dataObject->setLocation($location);
                }
                return $dataObject;
                break;
            case 'Location':
                return new App_DataObject_Location(self::getValidator('Location'), $properties);
                break;
            case 'Project':
                return new App_DataObject_Project(self::getValidator('Project'), $properties);
                break;
            case 'Link':
                return new App_DataObject_Link(self::getValidator('Link'), $properties);
                break;
            case 'Site':
                return new App_DataObject_Site(self::getValidator('Site'), $properties);
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataObjects mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
    }

    /**
     *
     * @return Vpfw_Database_Mysql
     */
    public static function getDatabase() {
        if (true == isset(self::$objectCache['Vpfw_Database_Mysql'])) {
            return self::$objectCache['Vpfw_Database_Mysql'];
        }

        if (false == class_exists('Vpfw_Database_Mysql')) {
            throw new Vpfw_Exception_Logical('Die Datenbankklasse mit dem Namen Vpfw_Database_Mysql existiert nicht');
        }

        self::$objectCache['Vpfw_Database_Mysql'] = new Vpfw_Database_Mysql(self::getConfig(), self::getLog());
        return self::$objectCache['Vpfw_Database_Mysql'];
    }

    /**
     *
     * @return Vpfw_Config_Abstract
     */
    public static function getConfig() {
        if (true == is_null(self::$configObject)) {
            throw new Vpfw_Exception_Logical('Die statische Factory Klasse hat das Konfigurationsobjekt nicht injiziert bekommen');
        }
        return self::$configObject;
    }

    public static function setConfig(Vpfw_Config_Abstract $config) {
        self::$configObject = $config;
    }

    public static function getLog() {
        if (true == isset(self::$objectCache['Logger'])) {
            return self::$objectCache['Logger'];
        }

        try {
            $logType = self::getConfig()->getValue('Log.Type');
        } catch (Vpfw_Exception_InvalidArgument $e) {
            $logType = 'file';
        }

        switch ($logType) {
            case 'file':
                self::$objectCache['Logger'] = new Vpfw_Log_File(self::getConfig());
                break;
            case 'mysql':
                self::$objectCache['Logger'] = new Vpfw_Log_Mysql(self::getConfig());
                break;
        }

        return self::$objectCache['Logger'];
    }

    /**
     *
     * @param string $name
     * @param string $action
     * @param Vpfw_View_Interface $view
     * @return Vpfw_Controller_Action_Interface
     */
    public static function getActionController($name, $action, Vpfw_View_Interface $view = null) {
        $className = 'App_Controller_Action_' . $name;
        if (false == class_exists($className)) {
            throw new Vpfw_Exception_Logical('Ein ActionController mit dem Namen ' . $name . ' existiert nicht');
        }
        $aC = new $className();
        $aC->setActionName($action);
        if (false == is_null($view)) {
            $aC->setView($view);
        } else {
            $aC->setView(self::getView($name, $aC->getActionName()));
        }
        return $aC;
    }

    public static function getView($controllerName, $actionName) {
        $viewPath = 'App/Html/' . $controllerName . '/' . $actionName . '.html';
        if (false == file_exists($viewPath)) {
            throw new Vpfw_Exception_Logical('Die Datei ' . $viewPath . ' existiert nicht');
        }

        return new Vpfw_View_Std($viewPath);
    }
}
