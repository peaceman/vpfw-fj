<?php
class Vpfw_Validator_Language {
    /**
     * @var Vpfw_DataMapper_Language
     */
    private $languageMapper;

    /**
     * @param Vpfw_DataMapper_Language $languageMapper
     */
    public function __construct(Vpfw_DataMapper_Language $languageMapper) {
        $this->languageMapper = $languageMapper;
    }

    public function validateShortName($shortName) {
        $strlen = strlen($shortName);
        if ($strlen != 3) {
            throw new Vpfw_Exception_Validation('Die Abkürzung einer Sprache muss 3 Zeichen lang sein');
        }
        if ($this->languageMapper->languageExists($shortName)) {
            throw new Vpfw_Exception_Validation('Eine Sprache mit der Abkürzung ' . $shortName . ' existiert schon');
        }
    }

    public function validateName($name) {
        $strlen = strlen($name);
        if (2 > $strlen || 64 < $strlen) {
            throw new Vpfw_Exception_Validation('Der Name einer Sprache muss mindestens 2 und maximal 64 Zeichen lang sein');
        }
    }
}