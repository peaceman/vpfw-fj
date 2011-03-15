<?php
class Vpfw_Language_Storage_Database implements Vpfw_Language_Storage_Interface {
    /**
     * @var Vpfw_DataMapper_Language
     */
    private $languageMapper;

    /**
     * @var Vpfw_DataObject_Language[]
     */
    private $languageDaos = array();

    /**
     * @param Vpfw_DataMapper_Language $languageMapper
     */
    public function __construct(Vpfw_DataMapper_Language $languageMapper) {
        $this->languageMapper = $languageMapper;
    }

    /**
     * @param string $shortLanguage
     * @param string $language
     */
    public function createLanguage($shortLanguage, $language) {
        $shortLanguage = strtoupper($shortLanguage);
        if ($this->languageMapper->languageExists($shortLanguage)) {
            throw new Vpfw_Exception_Logical('Die Sprache ' . $shortLanguage . ' kann nicht erstellt werden, da sie bereits existiert');
        }

        $languageDao = $this->languageMapper->createEntry(
                array(
                    'ShortName' => $shortLanguage,
                    'Name' => $language,
                ),
                true
        );
        $this->languageDaos[$shortLanguage] = $languageDao;
    }

    /**
     * @param string $shortLanguage
     * @return bool
     */
    public function hasDataForLanguage($shortLanguage) {
        $shortLanguage = strtoupper($shortLanguage);
        if (array_key_exists($shortLanguage, $this->languageDaos)) {
            return true;
        }

        if ($this->languageMapper->languageExists($shortLanguage)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $shortLanguage
     * @param string $languageVariable
     * @return mixed
     */
    public function get($shortLanguage, $languageVariable) {
        $shortLanguage = strtoupper($shortLanguage);
        $languageVariable = strtoupper($languageVariable);
        if (!array_key_exists($shortLanguage, $this->languageDaos)) {
            $this->loadLanguageFromDatabase($shortLanguage);
        }
        return $this->languageDaos[$shortLanguage]->getTranslation($languageVariable);
    }

    /**
     * @param string $shortLanguage
     * @param string $languageVariable
     * @param string $translation
     * @return Vpfw_Language_Storage_Database
     */
    public function set($shortLanguage, $languageVariable, $translation) {
        $shortLanguage = strtoupper($shortLanguage);
        $languageVariable = strtoupper($languageVariable);
        if (!array_key_exists($shortLanguage, $this->languageDaos)) {
            $this->loadLanguageFromDatabase($shortLanguage);
        }
        $this->languageDaos[$shortLanguage]->setTranslation($languageVariable, $translation);
        return $this;
    }

    /**
     * @param string $shortLanguage
     */
    private function loadLanguageFromDatabase($shortLanguage) {
        try {
            $this->languageDaos[$shortLanguage] = $this->languageMapper->getByShortName($shortLanguage);
        } catch (Vpfw_Exception_OutOfRange $e) {
            throw new Vpfw_Exception_Logical('Die Sprache mit der Abkürzung ' . $shortLanguage . ' sollte in der Datenbank existieren');
        }
    }
}
