<?php
class Vpfw_Language {
    /**
     * @var Vpfw_Language_Storage_Interface
     */
    private $languageStorage;

    /**
     * @var string
     */
    private $languageToUse;

    /**
     * @param Vpfw_Language_Storage_Interface $languageStorage
     */
    public function __construct(Vpfw_Language_Storage_Interface $languageStorage) {
        $this->languageStorage = $languageStorage;
    }

    /**
     * @param string $language
     * @return Vpfw_Language
     */
    public function setLanguageToUse($language) {
        if (!$this->languageStorage->hasDataForLanguage($language)) {
            throw new Vpfw_Exception_Critical('Es sind keine Daten für die Sprache ' . $language . ' hinterlegt');
        }
        $this->languageToUse = $language;
        return $this;
    }

    /**
     * @param string $language
     * @return Vpfw_Language
     */
    public function createLanguageAndUseIt($shortName, $name) {
        $this->languageStorage->createLanguage($shortName, $name);
        $this->languageToUse = $shortName;
        return $this;
    }

    /**
     * @param string $languageVariable
     * @return string
     */
    public function get($languageVariable) {
        $translation = $this->languageStorage->get($this->languageToUse, $languageVariable);
        return $translation === false ? $languageVariable : $translation;
    }

    /**
     * @param string $languageVariable
     * @param string $translation
     * @return Vpfw_Language
     */
    public function set($languageVariable, $translation) {
        $this->languageStorage->set($this->languageToUse, $languageVariable, $translation);
        return $this;
    }
}