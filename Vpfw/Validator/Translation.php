<?php
class Vpfw_Validator_Translation {
    /**
     * @var Vpfw_DataMapper_Translation
     */
    private $translationMapper;

    /**
     * @var Vpfw_DataMapper_Language
     */
    private $languageMapper;

    /**
     * @param Vpfw_DataMapper_Translation $translationMapper
     */
    public function __construct(Vpfw_DataMapper_Translation $translationMapper, Vpfw_DataMapper_Language $languageMapper) {
        $this->translationMapper = $translationMapper;
        $this->languageMapper = $languageMapper;
    }

    public function validateLanguageId($id) {
        if (!$this->languageMapper->entryWithFieldValuesExists(array('i|Id|' . $id))) {
            throw new Vpfw_Exception_Validation('Eine Sprache mit der Id ' . $id . ' existiert nicht');
        }
    }

    public function validateLanguageVariable($languageVariable) {
        $strlen = strlen($languageVariable);
        if (2 > $strlen || 64 < $strlen) {
            throw new Vpfw_Exception_Validation('Eine Sprachvariable muss mindestens 2 und maximal 64 Zeichen lang sein');
        }
    }

    public function validateText($text) {
        
    }
}
