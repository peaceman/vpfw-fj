<?php
class Vpfw_DataObject_Language extends Vpfw_DataObject_Abstract {
    /**
     * @var Vpfw_DataMapper_Translation
     */
    private $translationMapper;

    /**
     * @var Vpfw_DataObject_Translation[]
     */
    private $translations;

    /**
     * @param Vpfw_DataMapper_Translation $translationMapper
     */
    public function __construct(Vpfw_DataMapper_Translation $translationMapper, $properties = null) {
        $this->translationMapper = $translationMapper;
        $this->data = array(
            'Id' => null,
            'ShortName' => null,
            'Name' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => null, 'required' => true);
        }
        $this->translations = array(
            'byId' => array(),
            'byLanguageVariable' => array(),
        );
        $this->lazyLoadState = array(
            'Translations' => false,
        );
        parent::__construct($properties);
    }

    /**
     * @param string $shortName
     * @param bool $validate
     * @return Vpfw_DataObject_Language
     */
    public function setShortName($shortName, $validate = true) {
        if ($shortName != $this->getShortName()) {
            if ($validate == true) {
                $this->validator->validateShortName($shortName);
            }
            $this->setData('ShortName', $shortName);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName() {
        return $this->getData('ShortName');
    }

    /**
     * @param string $name
     * @param bool $validate
     * @return Vpfw_DataObject_Language
     */
    public function setName($name, $validate = true) {
        if ($name != $this->getName()) {
            if ($validate == true) {
                $this->validator->validateName($name);
            }
            $this->setData('Name', $name);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->getData('Name');
    }

    /**
     * @param string $languageVariable
     * @return Vpfw_DataObject_Translation
     */
    public function getTranslation($languageVariable) {
        $this->lazyLoadTranslations();
        if (array_key_exists($languageVariable, $this->translations['byLanguageVariable'])) {
            return $this->translations['byLanguageVariable'][$languageVariable];
        } else {
            false;
        }
    }

    /**
     * @param string $languageVariable
     * @param string $translation
     * @return Vpfw_DataObject_Language
     */
    public function setTranslation($languageVariable, $translation) {
        $this->lazyLoadTranslations();
        $translationDao = null;
        if (array_key_exists($languageVariable, $this->translations['byLanguageVariable'])) {
            $translationDao = $this->translations['byLanguageVariable'];
        } else {
            $translationDao = $this->translationMapper->createEntry();
            $translationDao->setLanguageId($this->getId());
            $translationDao->setLanguageVariable($languageVariable);
        }
        $translationDao->setText($translation);
        return $this;
    }

    private function lazyLoadTranslations() {
        if ($this->lazyLoadState['Translations'] === false) {
            $translations = $this->translationMapper->getByLanguageId($this->getId());
            foreach ($translations as $translation) {
                $this->translations['byId'][$translation->getId()] = $translation;
                $this->translations['byLanguageVariable'][$translation->getName()] = $translation;
            }
            $this->lazyLoadState['Translations'] = true;
        }
    }
}
