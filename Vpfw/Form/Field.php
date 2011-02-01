<?php
class Vpfw_Form_Field {
    const FRONT = 1;
    const END = 2;

    /**
     * Beinhaltet den Namen des Feldes, den es auch im
     * HTML-Formular haben sollte.
     * 
     * @var string
     */
    protected $name;

    /**
     * Diese Variable gibt Informationen darüber, ob dieses Feld
     * optional ist oder nicht.
     *
     * @var bool
     */
    protected $required;

    /**
     * Dieses Array beinhaltet die Validatoren, welche auf dieses
     * Feld angewandt werden sollen.
     *
     * @var Vpfw_Form_Validator_Interface[]
     */
    private $validators = array();

    /**
     * Dieses Array beinhaltet die Filter, welche auf dieses
     * Feld angewandt werden sollen.
     *
     * @var Vpfw_Form_Filter_Interface[]
     */
    private $filters = array();

    /**
     * Diese Variable speichert den empfangenen Wert aus dem
     * HTML-Formular nach der Validierung.
     *
     * @var string
     */
    protected $value;

    /**
     *
     * @param string $name
     * @param bool $required
     * @param Vpfw_Form_Validator_Interface[] $validators
     */
    public function __construct($name, $required = true) {
        $this->name = $name;
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isFilled() {
        if (false == is_null($this->value)) {
            if ('' == $this->value) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Mit dieser Methode hat man die Möglichkeit, die Validatoren
     * für dieses Feld in einem Rutsch zu setzen. Sollten diesem Feld
     * schon Validatoren zugewiesen worden sein, werden diese gelöscht.
     *
     * @param Vpfw_Form_Validator_Interface[] $validators
     * @return Vpfw_Form_Field
     */
    public function setValidators(array $validators) {
        foreach ($validators as $validator) {
            /* @var $validator Vpfw_Form_Validator_Interface */
            if (false == $validator instanceof Vpfw_Form_Validator_Interface) {
                throw new Vpfw_Exception_InvalidArgument('Ein Element in dem übergebenen Array ist kein FormValidator');
            }
        }
        $this->validators = $validators;
        return $this;
    }

    /**
     * Mit dieser Methode kann man einen Validator zu den Validatoren
     * für dieses Feld hinzufügen.
     *
     * @param Vpfw_Form_Validator_Interface $validator
     * @param int $position
     * @return Vpfw_Form_Field
     */
    public function addValidator(Vpfw_Form_Validator_Interface $validator, $position = self::END) {
        switch ($position) {
            case self::FRONT:
                array_unshift($this->validators, $validator);
                break;
            case self::END:
                $this->validators[] = $validator;
                break;
        }
        return $this;
    }

    /**
     * Mit dieser Methode hat man die Möglichkeit, die Filter
     * für dieses Feld in einem Rutsch zu setzen. Sollten diesem Feld
     * schon Filter zugewiesen worden sein, werden diese gelöscht.
     *
     * @param Vpfw_Form_Filter_Interface[] $filters
     * @return Vpfw_Form_Field
     */
    public function setFilters(array $filters) {
        foreach ($filters as $filter) {
            /* @var $filter Vpfw_Form_Filter_Interface */
            if (false == $filter instanceof Vpfw_Form_Filter_Interface) {
                throw new Vpfw_Exception_InvalidArgument('Ein Element in dem übergebenen Array ist kein FormFilter');
            }
        }
        $this->filters = $filters;
        return $this;
    }

    /**
     * Mit dieser Methode kann man einen Filter zu den Filtern
     * für dieses Feld hinzufügen.
     *
     * @param Vpfw_Form_Filter_Interface $filter
     * @param int $position
     * @return Vpfw_Form_Field
     */
    public function addFilter(Vpfw_Form_Filter_Interface $filter, $position = self::END) {
        switch ($position) {
            case self::FRONT:
                array_unshift($this->filters, $filter);
                break;
            case self::END:
                $this->filters[] = $filter;
                break;
        }
        return $this;
    }

    /**
     * @param string $value Wert aus dem HTML-Formular
     * @return Vpfw_Form_Field
     */
    public function setValue($value) {
        $this->value = (string)$value;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * Führt die definierten Validatoren aus
     *
     * @return mixed True wenn alle Validierungen ohne Fehler durchgelaufen sind oder ein Array aus Fehlermeldungen der Validatoren
     */
    public function executeValidators() {
        if (true == is_null($this->value)) {
            throw new Vpfw_Exception_Logical('Die Validatoren sollten nicht ausgeführt werden, wenn das Value sowieso keinen Wert enthält');
        }
        $validationErrors = array();
        foreach ($this->validators as $validator) {
            $validationResult = $validator->run($this->value);
            if (true !== $validationResult) {
                $validationErrors[] = $validationResult;
            }
        }
        return 0 == count($validationErrors) ? true : $validationErrors;
    }

    /**
     * Wendet die definierten Filter auf $this->value an
     *
     * @return Vpfw_Form_Field
     */
    public function executeFilters() {
        if (true == is_null($this->value)) {
            throw new Vpfw_Exception_Logical('Die Filter sollten nicht ausgeführt werden, wenn das Value sowieso keinen Wert enthält');
        }
        foreach ($this->filters as $filter) {
            $this->value = $filter->run($this->value);
        }
        return $this;
    }

    public function fillView() {
        $viewArray = array();
        $viewArray[$this->getName() . '-value'] = $this->getValue();
        return $viewArray;
    }
}