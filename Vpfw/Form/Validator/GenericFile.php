<?php
class Vpfw_Form_Validator_GenericFile implements Vpfw_Form_Validator_Interface {
    public function run($value) {
        if (UPLOAD_ERR_OK === $value['error']) {
            return true;
        } else {
            switch ($value['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    return 'Die hochgeladene Datei darf maximal ' . ini_get('upload_max_filesize') . ' Bytes groß sein';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    return 'Die hochgeladene Datei ist zu groß';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    return 'Die Datei wurde nur teilweise hochgeladen';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return 'Es wurde keine Datei hochgeladen';
                    break;
            }
            return false;
        }
    }
}
