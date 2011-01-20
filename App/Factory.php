<?php
class App_Factory {
    public static function getValidator($type) {
        switch ($type) {
            case 'Picture':
                return new App_Validator_Picture();
                break;
            case 'Session':
                return new App_Validator_Session();
                break;
            case 'PictureComment':
                return new App_Validator_PictureComment();
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des Validators mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
        }
    }

    public static function getDataMapper($type) {
        $className = 'App_DataMapper_' . $type;
        switch ($type) {
            case 'User':
                return new App_DataMapper_User(Vpfw_Factory::getDatabase());
                break;
            case 'Deletion':
                return new App_DataMapper_Deletion(Vpfw_Factory::getDatabase());
                break;
            case 'Session':
                return new App_DataMapper_Session(Vpfw_Factory::getDatabase());
                break;
            case 'Picture':
                return new App_DataMapper_Picture(Vpfw_Factory::getDatabase());
                break;
            case 'RuleViolation':
                return new App_DataMapper_RuleViolation(Vpfw_Factory::getDatabase());
                break;
            case 'PictureComment':
                return new App_DataMapper_PictureComment(Vpfw_Factory::getDatabase());
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataMappers mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
    }

    public static function getDataObject($type, $properties = null) {
        switch ($type) {
            case 'User':
                $deletion = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['DelSessionId'])) {
                        try {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->getEntryById($properties['DeletionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->createEntry(
                                    array('Id' => $properties['DeletionId'],
                                          'SessionId' => $properties['DelSessionId'],
                                          'Time' => $properties['DelTime'],
                                          'Reason' => $properties['DelReason'])
                            );
                        }
                        unset($properties['DelSessionId']);
                        unset($properties['DelTime']);
                        unset($properties['DelReason']);
                    }
                }
                $dataObject = new App_DataObject_User(Vpfw_Factory::getValidator('User'), $properties);
                if (false == is_null($deletion)) {
                    $dataObject->setDeletion($deletion);
                }
                return $dataObject;
                break;
            case 'Deletion':
                $session = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['Ip'])) {
                        try {
                            $session = Vpfw_Factory::getDataMapper('Session')->getEntryById($properties['SessionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $session = Vpfw_Factory::getDataMapper('Session')->createEntry(
                                array('Id' => $properties['SessionId'],
                                      'UserId' => $properties['UserId'],
                                      'Ip' => $properties['Ip'],
                                      'StartTime' => $properties['StartTime'],
                                      'LastRequest' => $properties['LastRequest'],
                                      'Hits' => $properties['Hits'],
                                      'UserAgent' => $properties['UserAgent'])
                            );
                        }
                        unset($properties['Ip'],
                              $properties['StartTime'],
                              $properties['LastRequest'],
                              $properties['Hits'],
                              $properties['UserAgent']);
                    }
                }
                $dataObject = new App_DataObject_Deletion(Vpfw_Factory::getValidator('Deletion'), $properties);
                if (false == is_null($session)) {
                    $dataObject->setSession($session);
                }
                return $dataObject;
                break;
            case 'Session':
                $user = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['CreationTime'])) {
                        try {
                            $user = Vpfw_Factory::getDataMapper('User')->getEntryById($properties['UserId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $user = Vpfw_Factory::getDataMapper('User')->createEntry(
                                array(
                                    'Id' => $properties['UserId'],
                                    'CreationTime' => $properties['CreationTime'],
                                    'CreationIp' => $properties['CreationIp'],
                                    'DeletionId' => $properties['DeletionId'],
                                    'Username' => $properties['Username'],
                                    'Passhash' => $properties['Passhash'],
                                    'Email' => $properties['Email'],
                                )
                            );
                        }
                        unset($properties['CreationTime'],
                              $properties['CreationIp'],
                              $properties['DeletionId'],
                              $properties['Username'],
                              $properties['Passhash'],
                              $properties['Email']);
                    }
                }
                $dataObject = new App_DataObject_Session(Vpfw_Factory::getValidator('Session'), $properties);
                if (false == is_null($user)) {
                    $dataObject->setUser($user);
                }
                return $dataObject;
                break;
            case 'Picture':
                $session = null;
                $deletion = null;
                if (false == is_null($properties)) {
                    /*
                     * Wenn wir Informationen über die Session bekommen haben,
                     * wird daraus ein DataObject erzeugt.
                     */
                    if (true == isset($properties['SesIp'])) {
                        try {
                            $session = Vpfw_Factory::getDataMapper('Session')->getEntryById($properties['SessionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $session = Vpfw_Factory::getDataMapper('Session')->createEntry(
                                array(
                                    'Id' => $properties['SessionId'],
                                    'UserId' => $properties['SesUserId'],
                                    'Ip' => $properties['SesIp'],
                                    'StartTime' => $properties['SesStartTime'],
                                    'LastRequest' => $properties['SesLastRequest'],
                                    'Hits' => $properties['SesHits'],
                                    'UserAgent' => $properties['SesUserAgent'],
                                )
                            );
                        }
                        /*
                         * Löschen der Sessionbezogenen Daten aus dem Eigenschaften-
                         * array des Bildes
                         */
                        unset($properties['SesUserId'],
                              $properties['SesIp'],
                              $properties['SesStartTime'],
                              $properties['SesLastRequest'],
                              $properties['SesHits'],
                              $properties['SesUserAgent']);
                    }
                    /*
                     * Wenn wir Informationen über die Löschung bekommen haben,
                     * wird daraus ein DataObject erzeugt.
                     */
                    if (true == isset($properties['DelSessionId'])) {
                        try {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->getEntryById($properties['DeletionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->createEntry(
                                array(
                                    'Id' => $properties['DeletionId'],
                                    'SessionId' => $properties['DelSessionId'],
                                    'Time' => $properties['DelTime'],
                                    'Reason' => $properties['DelReason'],
                                )
                            );
                        }
                        /*
                         * Löschen der Löschungsbezogenen Daten aus dem Eigenschaften-
                         * array des Bildes
                         */
                        unset($properties['DelSessionId'],
                              $properties['DelTime'],
                              $properties['DelReason']);
                    }
                }
                $dataObject = new App_DataObject_Picture(Vpfw_Factory::getValidator('Picture'), Vpfw_Factory::getDataMapper('PictureComment'), $properties);
                if (false == is_null($session)) {
                    $dataObject->setSession($session);
                }
                if (false == is_null($deletion)) {
                    $dataObject->setDeletion($deletion);
                }
                return $dataObject;
                break;
            case 'RuleViolation':
                $picture = null;
                $session = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['PicMd5'])) {
                        try {
                            $picture = Vpfw_Factory::getDataMapper('Picture')->getEntryById($properties['PictureId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $picture = Vpfw_Factory::getDataMapper('Picture')->createEntry(
                                array(
                                    'Id' => $properties['PictureId'],
                                    'Md5' => $properties['PicMd5'],
                                    'Gender' => $properties['PicGender'],
                                    'SessionId' => $properties['PicSessionId'],
                                    'UploadTime' => $properties['PicUploadTime'],
                                    'SiteHits' => $properties['PicSiteHits'],
                                    'PositiveRating' => $properties['PicPositiveRating'],
                                    'NegativeRating' => $properties['PicNegativeRating'],
                                    'DeletionId' => $properties['PicDeletionId']
                                )
                            );
                        }
                        unset($properties['PicMd5'],
                              $properties['PicGender'],
                              $properties['PicSessionId'],
                              $properties['PicUploadTime'],
                              $properties['PicSiteHits'],
                              $properties['PicPositiveRating'],
                              $properties['PicNegativeRating'],
                              $properties['PicDeletionId']);
                    }
                    if (true == isset($properties['SesUserId'])) {
                        try {
                            $session = Vpfw_Factory::getDataMapper('Session')->getEntryById($properties['SessionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $session = Vpfw_Factory::getDataMapper('Session')->createEntry(
                                array(
                                    'Id' => $properties['SessionId'],
                                    'UserId' => $properties['SesUserId'],
                                    'Ip' => $properties['SesIp'],
                                    'StartTime' => $properties['SesStartTime'],
                                    'LastRequest' => $properties['SesLastRequest'],
                                    'Hits' => $properties['SesHits'],
                                    'UserAgent' => $properties['SesUserAgent'],
                                )
                            );
                        }
                        unset($properties['SesUserId'],
                              $properties['SesIp'],
                              $properties['SesStartTime'],
                              $properties['SesLastRequest'],
                              $properties['SesHits'],
                              $properties['SesUserAgent']);
                    }
                }
                $dataObject = new App_DataObject_RuleViolation(Vpfw_Factory::getValidator('RuleViolation'), $properties);
                if (false == is_null($picture)) {
                    $dataObject->setPicture($picture);
                }
                if (false == is_null($session)) {
                    $dataObject->setSession($session);
                }
                return $dataObject;
                break;
            case 'PictureComment':
                $session = null;
                $picture = null;
                $deletion = null;
                if (false == is_null($properties)) {
                    if (true == isset($properties['SesIp'])) {
                        try {
                            $session = Vpfw_Factory::getDataMapper('Session')->getEntryById($properties['SessionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $session = Vpfw_Factory::getDataMapper('Session')->createEntry(
                                array(
                                    'Id' => $properties['SessionId'],
                                    'UserId' => $properties['SesUserId'],
                                    'Ip' => $properties['SesIp'],
                                    'StartTime' => $properties['SesStartTime'],
                                    'LastRequest' => $properties['SesLastRequest'],
                                    'Hits' => $properties['SesHits'],
                                    'UserAgent' => $properties['SesUserAgent'],
                                )
                            );
                        }
                        unset($properties['SesUserId'],
                              $properties['SesIp'],
                              $properties['SesStartTime'],
                              $properties['SesLastRequest'],
                              $properties['SesHits'],
                              $properties['SesUserAgent']);
                    }
                    if (true == isset($properties['PicMd5'])) {
                        try {
                            $picture = Vpfw_Factory::getDataMapper('Picture')->getEntryById($properties['PictureId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $picture = Vpfw_Factory::getDataMapper('Picture')->createEntry(
                                array(
                                    'Id' => $properties['PictureId'],
                                    'Md5' => $properties['PicMd5'],
                                    'Gender' => $properties['PicGender'],
                                    'SessionId' => $properties['PicSessionId'],
                                    'UploadTime' => $properties['PicUploadTime'],
                                    'SiteHits' => $properties['PicSiteHits'],
                                    'PositiveRating' => $properties['PicPositiveRating'],
                                    'NegativeRating' => $properties['PicNegativeRating'],
                                    'DeletionId' => $properties['PicDeletionId']
                                )
                            );
                        }
                        unset($properties['PicMd5'],
                              $properties['PicGender'],
                              $properties['PicSessionId'],
                              $properties['PicUploadTime'],
                              $properties['PicSiteHits'],
                              $properties['PicPositiveRating'],
                              $properties['PicNegativeRating'],
                              $properties['PicDeletionId']);
                    }
                    if (true == isset($properties['DelSessionId'])) {
                        try {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->getEntryById($properties['DeletionId'], false);
                        } catch (Vpfw_Exception_OutOfRange $e) {
                            $deletion = Vpfw_Factory::getDataMapper('Deletion')->createEntry(
                                array(
                                    'Id' => $properties['DeletionId'],
                                    'SessionId' => $properties['DelSessionId'],
                                    'Time' => $properties['DelTime'],
                                    'Reason' => $properties['DelReason'],
                                )
                            );
                        }
                        unset($properties['DelSessionId'],
                              $properties['DelTime'],
                              $properties['DelReason']);
                    }
                }
                $dataObject = new App_DataObject_PictureComment(Vpfw_Factory::getValidator('PictureComment'), $properties);
                if (false == is_null($deletion)) {
                    $dataObject->setDeletion($deletion);
                }
                if (false == is_null($session)) {
                    $dataObject->setSession($session);
                }
                if (false == is_null($picture)) {
                    $dataObject->setPicture($picture);
                }
                return $dataObject;
                break;
            default:
                throw new Vpfw_Exception_Logical('Die Abhängigkeiten des DataObjects mit dem Typ ' . $type . ' konnten nicht aufgelöst werden');
                break;
        }
    }
}