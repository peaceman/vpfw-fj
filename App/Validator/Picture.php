<?php
class App_Validator_Picture {
    /**
     * @var App_DataMapper_Session
     */
    private $sessionMapper;

    public function __construct(App_DataMapper_Session $sessionMapper) {
        $this->sessionMapper = $sessionMapper;
    }

    /**
     *
     * @param string $value
     */
    public function validateGender($value) {
        switch ($value) {
            case 'male':
            case 'female':
                break;
            default:
                throw new Vpfw_Exception_Validation('Unbekanntes Geschlecht ' . $gender);
        }
    }

    public function validateMd5($hash) {
        if (false == (!empty($hash) && 0 !== preg_match('/^[a-f0-9]{32}$/', $hash))) {
            throw new Vpfw_Exception_Validation('Der MD5-Hash des Passwortes ist ungültig');
        }
    }

    public function validateSessionId($id) {
        if (false == $this->sessionMapper->entryWithFieldValuesExists(array('i|Id|' . $id))) {
            throw new Vpfw_Exception_Validation('Eine Session mit der Id ' . $id . ' ist nicht bekannt');
        }
    }

    public function validateUploadTime($timestamp) {
        if (((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX)) {
            throw new Vpfw_Exception_Validation('Der Timestamp ' . $time . ' ist ungültig');
        }
    }

    public function validateSiteHits($hits) {
        if (false == is_numeric($hits)) {
            throw new Vpfw_Exception_Validation('Die Angabe der Hits eines Bildes sollte numerisch sein');
        }
        if (0 > $hits) {
            throw new Vpfw_Exception_Validation('Die Angabe der Hits eines Bildes kann nicht negativ sein');
        }
    }
}
