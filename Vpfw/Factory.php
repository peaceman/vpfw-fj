<?php
class Vpfw_Factory {
    public static $objectCache = array();
    /**
     *
     * @var Vpfw_Config_Abstract 
     */
    private static $configObject;

    public static function getAuthSession(Vpfw_Request_Interface $request) {
        return App_Factory::getAuthSession($request);
    }

    public static function getAuthAdapter($type) {
        $classNameExtern = 'App_Auth_Adapter_' . $type;
        $classNameIntern = 'Vpfw_Auth_Adapter_' . $type;
        if (true == isset(self::$objectCache[$classNameIntern])) {
            return self::$objectCache[$classNameIntern];
        }
        if (true == isset(self::$objectCache[$classNameExtern])) {
            return self::$objectCache[$classNameExtern];
        }

        if (false == class_exists($classNameIntern) && false == class_exists($classNameExtern)) {
            throw new Vpfw_Exception_Logical('Einen AuthAdapter mit dem Typen ' . $type . ' gibt es nicht');
        }

        switch ($type) {
            case 'Database':
                return self::$objectCache[$classNameIntern] = new Vpfw_Auth_Adapter_Database(self::getDataMapper('User'));
                break;
            default:
                return self::$objectCache[$classNameExtern] = App_Factory::getAuthAdapter($type);
        }
    }

    public static function getAuthStorage($type) {
        $classNameExtern = 'App_Auth_Storage_' . $type;
        $classNameIntern = 'Vpfw_Auth_Storage_' . $type;
        if (true == isset(self::$objectCache[$classNameIntern])) {
            return self::$objectCache[$classNameIntern];
        }
        if (true == isset(self::$objectCache[$classNameExtern])) {
            return self::$objectCache[$classNameExtern];
        }

        if (false == class_exists($classNameIntern) && false == class_exists($classNameExtern)) {
            throw new Vpfw_Exception_Logical('Ein AuthStorage mit dem Typen ' . $type . ' gibt es nicht');
        }

        switch($type) {
            case 'Session':
                return self::$objectCache[$classNameIntern] = new Vpfw_Auth_Storage_Session(self::getConfig()->getValue('Session.Name'));
                break;
            default:
                return self::$objectCache[$classNameExtern] = App_Factory::getAuthStorage($type);
        }
    }
    public static function getValidator($type) {
        $classNameExtern = 'App_Validator_' . $type;
        $classNameIntern = 'Vpfw_Validator_'  . $type;
        if (true == isset(self::$objectCache[$classNameIntern])) {
            return self::$objectCache[$classNameIntern];
        }
        if (true == isset(self::$objectCache[$classNameExtern])) {
            return self::$objectCache[$classNameExtern];
        }

        if (false == class_exists($classNameIntern) && false == class_exists($classNameExtern)) {
            throw new Vpfw_Exception_Logical('Eine Validator des Typs ' . $type . ' existiert nicht');
        }

        switch($type) {
            case 'RbacRole':
                return self::$objectCache[$classNameIntern] = new Vpfw_Validator_RbacRole(self::getDataMapper('RbacRole'));
                break;
            case 'RbacObject':
                return self::$objectCache[$classNameIntern] = new Vpfw_Validator_RbacObject(self::getDataMapper('RbacObject'));
                break;
            case 'RbacPermission':
                return self::$objectCache[$classNameIntern] = new Vpfw_Validator_RbacPermission();
            default:
               return self::$objectCache[$classNameExtern] = App_Factory::getValidator($type);
        }
    }

    /**
     *
     * @param string $type
     * @return Vpfw_DataMapper_Interface
     */
    public static function getDataMapper($type) {
        $classNameExtern = 'App_DataMapper_' . $type;
        $classNameIntern = 'Vpfw_DataMapper_' . $type;
        if (true == isset(self::$objectCache[$classNameExtern])) {
            return self::$objectCache[$classNameExtern];
        } elseif (true == isset(self::$objectCache[$classNameIntern])) {
            return self::$objectCache[$classNameIntern];
        }

        if (false == class_exists($classNameExtern) && false == class_exists($classNameIntern)) {
            throw new Vpfw_Exception_Logical('Ein DataMapper des Typs ' . $type . ' existiert nicht');
        }

        switch ($type) {
            case 'RbacObject':
                return self::$objectCache[$classNameIntern] = new Vpfw_DataMapper_RbacObject(self::getDatabase());
                break;
            case 'RbacPermission':
                return self::$objectCache[$classNameIntern] = new Vpfw_DataMapper_RbacPermission(self::getDatabase());
                break;
            case 'RbacRole':
                return self::$objectCache[$classNameIntern] = new Vpfw_DataMapper_RbacRole(self::getDatabase());
                break;
            default:
                return self::$objectCache[$classNameExtern] = App_Factory::getDataMapper($type);
        }
    }

    /**
     * @static
     * @throws Vpfw_Exception_Logical
     * @param  $type
     * @param  $properties
     * @return Vpfw_DataObject_Interface
     */
    public static function getDataObject($type, $properties = null) {
        $classNameExtern = 'Vpfw_DataObject_' . $type;
        $classNameIntern = 'App_DataObject_' . $type;
        if (false == class_exists($classNameIntern) && false == class_exists($classNameExtern)) {
            throw new Vpfw_Exception_Logical('Ein DataObject des Typs ' . $type . ' existiert nicht');
        }
        switch ($type) {
            case 'RbacObject':
                return new Vpfw_DataObject_RbacObject(self::getValidator('RbacObject'), $properties);
                break;
            case 'RbacPermission':
                $roleDao = null;
                $objectDao = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['RoleName'])) {
                        try {
                            $roleDao = Vpfw_Factory::getDataMapper('RbacRole')->getEntryById($properties['RoleId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $roleDao = Vpfw_Factory::getDataMapper('RbacRole')->createEntry(
                                array(
                                    'Id' => $properties['RoleId'],
                                    'Name' => $properties['RoleName'],
                                )
                            );
                        }
                        unset($properties['RoleName']);
                    }

                    if (true == isset($properties['ObjectDefault'])) {
                        try {
                            $objectDao = Vpfw_Factory::getDataMapper('RbacObject')->getEntryById($properties['ObjectId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $objectDao = Vpfw_Factory::getDataMapper('RbacObject')->createEntry(
                                array(
                                    'Id' => $properties['ObjectId'],
                                    'Default' => $properties['ObjectDefault'],
                                    'Name' => $properties['ObjectName'],
                                )
                            );
                        }
                        unset($properties['ObjectDefault'],
                              $properties['ObjectName']);
                    }
                }
                $dataObject = new Vpfw_DataObject_RbacPermission(Vpfw_Factory::getValidator('RbacPermission'), Vpfw_Factory::getDataMapper('RbacRole'), Vpfw_Factory::getDataMapper('RbacObject'), $properties);
                if (false == is_null($roleDao)) {
                    $dataObject->setRole($roleDao);
                }
                if (false == is_null($objectDao)) {
                    $dataObject->setObject($objectDao);
                }
                return $dataObject;
                break;
            case 'RbacRole':
                /* @var $permissions Vpfw_DataObject_RbacPermission[] */
                $permissions = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['PermIds'])) {
                        $permissions = array();
                        $permIds = explode(',', $properties['PermIds']);
                        $permObjectIds = explode(',', $properties['PermObjectIds']);
                        $permStates = explode(',', $properties['PermStates']);
                        foreach ($permIds as $key => $permId) {
                            try {
                                $permission = Vpfw_Factory::getDataMapper('RbacPermission')->getEntryById($permId, false);
                            } catch (Vpfw_Exception_OutOfRange $e) {
                                $permission = Vpfw_Factory::getDataMapper('RbacPermission')->createEntry(
                                    array(
                                        'Id' => $permId,
                                        'RoleId' => $properties['Id'],
                                        'ObjectId' => $permObjectIds[$key],
                                        'State' => $permStates[$key],
                                    )
                                );
                            }
                            $permissions[] = $permission;
                        }
                    }
                }
                unset($properties['PermIds'],
                      $properties['PermObjectIds'],
                      $properties['PermStates']);
                $dataObject = new Vpfw_DataObject_RbacRole(self::getDataMapper('RbacPermission'), Vpfw_Factory::getValidator('RbacRole'), $properties);
                if (false == is_null($permissions)) {
                    $dataObject->setPermissions($permissions);
                }
                return $dataObject;
                break;
        }
        return App_Factory::getDataObject($type, $properties);
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

    public static function getRbacRole(Vpfw_DataObject_RbacRole $role) {
        return new Vpfw_Rbac_Role($role, self::getDataMapper('RbacObject'), self::getDataMapper('RbacPermission'));
    }

    public static function getRbacUser(App_DataObject_User $user = null) {
        return new Vpfw_Rbac_User(self::getDataMapper('RbacObject'), $user);
    }
}
