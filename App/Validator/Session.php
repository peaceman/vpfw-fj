<?php
class App_Validator_Session {
    public function validateIp($ip) {
        if (false == filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new Vpfw_Exception_Validation('Die IP-Adresse ' . $ip . ' ist ungültig');
        }
    }

    public function validateStartTime($timestamp) {
        if (((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX)) {
            throw new Vpfw_Exception_Validation('Der Timestamp ' . $time . ' ist ungültig');
        }
    }

    public function validateLastRequest($timestamp) {
        if (((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX)) {
            throw new Vpfw_Exception_Validation('Der Timestamp ' . $time . ' ist ungültig');
        }
    }

    public function validateHits($hits) {
        if (false == is_numeric($hits)) {
            throw new Vpfw_Exception_Validation('Die Hits sollten in Form einer Zahl angegeben werden');
        }
        if (1 > $hits) {
            throw new Vpfw_Exception_Validation('Die Anzahl der Hits muss mindestens eins betragen');
        }
    }

    public function validateUserAgent() {
        //TODO wie zur hölle soll man einen useragent validieren können?
    }
}