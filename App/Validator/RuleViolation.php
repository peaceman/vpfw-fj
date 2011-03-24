<?php
class App_Validator_RuleViolation {
    private $pictureMapper;
    private $sessionMapper;

    public function __construct(App_DataMapper_Picture $pictureMapper, App_DataMapper_Session $sessionMapper) {
        $this->pictureMapper = $pictureMapper;
        $this->sessionMapper = $sessionMapper;
    }
    public function validateReason($reason) {

    }

    public function validatePictureId($id) {
        try {
            $this->pictureMapper->getEntryById($id, false);
        } catch (Vpfw_Exception_OutOfRange $e) {
            if (!$this->pictureMapper->entryWithFieldValuesExists(array('i|Id|' . $id))) {
                throw new Vpfw_Exception_Validation('Ein Bild mit der Id ' . $id . ' existiert nicht');
            }
        }
    }

    public function validateSessionId($id) {
        if (false == $this->sessionMapper->entryWithFieldValuesExists(array('i|Id|' . $id))) {
            throw new Vpfw_Exception_Validation('Eine Session mit der Id ' . $id . ' ist nicht bekannt');
        }
    }

    public function validateTime($time) {
        if (0 > $time) {
            throw new Vpfw_Exception_Validation('Ein Timestamp sollte nicht negativ sein');
        }
    }

    public function validateHandled($handled) {
        if ($handled != 0 && $handled != 1) {
            throw new Vpfw_Exception_Validation('Der Wert Handled der RuleViolation muss entweder den Wert 0 oder den Wert 1 besitzen');
        }
    }
}
