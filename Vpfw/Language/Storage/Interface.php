<?php
interface Vpfw_Language_Storage_Interface {
    /**
     * @param string $shortLanguage
     * @return bool
     */
    public function hasDataForLanguage($shortLanguage);
    
    /**
     * @param string $shortLanguage
     * @param string $languageVariable
     * @return mixed if no translation is available false, otherwise the translation as string
     */
    public function get($shortLanguage, $languageVariable);

    /**
     * @param string $shortLanguage
     * @param string $languageVariable
     * @param string $translation
     * @return Vpfw_Language_Storage_Interface
     */
    public function set($shortLanguage, $languageVariable, $translation);

    /**
     * @param string $shortLanguage
     * @param string $language
     * @return Vpfw_Language_Storage_Interface
     */
    public function createLanguage($shortLanguage, $language);
}