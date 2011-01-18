<?php
class App_Factory {
    public static function getValidator($type) {
        switch ($type) {
            case 'User':
                return new App_Validator_User(Vpfw_Factory::getModel('User'));
                break;
            case 'Location':
                return new App_Validator_Location(Vpfw_Factory::getDataMapper('Location'));
                break;
            case 'Event':
                return new App_Validator_Event(Vpfw_Factory::getDataMapper('Event'), Vpfw_Factory::getDataMapper('Location'));
                break;
            case 'Project':
                return new App_Validator_Project(Vpfw_Factory::getDataMapper('Project'));
                break;
            case 'Link':
                return new App_Validator_Link(Vpfw_Factory::getDataMapper('Link'));
                break;
            case 'Site';
                return new App_Validator_Site(Vpfw_Factory::getDataMapper('Site'));
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des Validators mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
        }
    }

    public static function getDataMapper($type) {
        switch ($type) {
            case 'User':
                return new App_DataMapper_User(Vpfw_Factory::getDatabase());
                break;
            case 'Event':
                return new App_DataMapper_Event(Vpfw_Factory::getDatabase());
                break;
            case 'Location':
                return new App_DataMapper_Location(Vpfw_Factory::getDatabase());
                break;
            case 'Project':
                return new App_DataMapper_Project(Vpfw_Factory::getDatabase());
                break;
            case 'Link':
                return new App_DataMapper_Link(Vpfw_Factory::getDatabase());
                break;
            case 'Site':
                return new App_DataMapper_Site(Vpfw_Factory::getDatabase());
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataMappers mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
    }

    public static function getDataObject($type, $properties = null) {
        switch ($type) {
            case 'User':
                return new App_DataObject_User(Vpfw_Factory::getValidator('User'), $properties);
                break;
            case 'Event':
                $location = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['LocationId'])) {
                        try {
                            $location = Vpfw_Factory::getDataMapper('Location')->getEntryById($properties['LocationId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $location = Vpfw_Factory::getDataMapper('Location')->createEntry(array('Id' => $properties['LocationId'], 'Name' => $properties['LocationName']));
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
                $dataObject = new App_DataObject_Event(Vpfw_Factory::getValidator('Event'), $properties);
                if (false == is_null($location)) {
                    $dataObject->setLocation($location);
                }
                return $dataObject;
                break;
            case 'Location':
                return new App_DataObject_Location(Vpfw_Factory::getValidator('Location'), $properties);
                break;
            case 'Project':
                return new App_DataObject_Project(Vpfw_Factory::getValidator('Project'), $properties);
                break;
            case 'Link':
                return new App_DataObject_Link(Vpfw_Factory::getValidator('Link'), $properties);
                break;
            case 'Site':
                return new App_DataObject_Site(Vpfw_Factory::getValidator('Site'), $properties);
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataObjects mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
    }
}