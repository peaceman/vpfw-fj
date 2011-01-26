<?php
class Vpfw_Form {
    /**
     * Array bestehend aus den von diesem Formular
     * zu verwaltenden Felder
     *
     * @var Vpfw_Form_Field[]
     */
    private $fields = array();

    /**
     * @var Vpfw_Request_Interface
     */
    private $request;

    /**
     * @var Vpfw_View_Interface
     */
    private $view;

    /**
     * @var array
     */
    private $errorMessages;

    /**
     * @var bool
     */
    private $allIsValid = true;

    /**
     * @var string
     */
    private $name;


    /**
     * @param Vpfw_Request_Interface $request
     * @param string $name Name des HTML-Formulars
     * @param array $fields
     * @param Vpfw_View_Interface $view
     */
    public function __construct(Vpfw_Request_Interface $request, $name, array $fields, Vpfw_View_Interface $view) {
        $this->request = $request;
        $this->view = $view;
        $this->name = $name;
        $this->errorMessages = array(
            'form' => array(),
            'field' => array(),
        );
        foreach ($fields as $field) {
            if (false == $field instanceof Vpfw_Form_Field) {
                throw new Vpfw_Exception_InvalidArgument('Einem Formularobjekt dürfen nur Formularfeldobjekte übergeben werden');
            }
            $this->fields[$field->getName()] = $field;
        }
        $this->handleRequest();
    }

    /**
     * @return bool
     */
    public function isAllValid() {
        return $this->allIsValid;
    }

    public function getValidValues() {
        $returnValues = array();
        foreach ($this->fields as $field) {
            /* @var $field Vpfw_Form_Field */
            if ($field->isRequired()) {
                $returnValues[$field->getName()] = $field->getValue();
            } else {
                $value = $field->getValue();
                if (false == is_null($value)) {
                    $returnValues[$field->getName()] = $field->getValue();
                }
            }
        }
        return $returnValues;
    }

    private function handleRequest() {
        if (true == $this->formWasSent()) {
            $this->checkExistanceOfAllRequiredFields();
            $this->processSentFields();
        }
        $this->fillView();
    }

    private function formWasSent() {
        if (true == $this->request->issetParameter('form-' . $this->name)) {
            return true;
        } else {
            return false;
        }
    }

    private function checkExistanceOfAllRequiredFields() {
        foreach ($this->fields as $field) {
            /* @var $field Vpfw_Form_Field */
            if (true == $field->isRequired()) {
                if (false == $this->request->issetParameter($field->getName())) {
                    $this->errorMessages['form'][] = 'Das Feld ' . $field->getName() . ' muss zwingend ausgefüllt werden';
                    $this->allIsValid = false;
                }
            }
        }
    }

    private function processSentFields() {
        foreach ($this->fields as $field) {
            /* @var $field Vpfw_Form_Field */
            if (true == $this->request->issetParameter($field->getName())) {
                $validationResult = $field->setValue($this->request->getParameter($field->getName()))
                                          ->executeFilters()
                                          ->executeValidators();
                if (true !== $validationResult) {
                    $this->errorMessages['field'][$field->getName()] = $validationResult;
                    $this->allIsValid = false;
                }
            }
        }
    }

    private function fillView() {
        $viewArray = array();
        foreach ($this->fields as $field) {
            $viewArray[$field->getName() . '-value'] = $field->getValue();
            $viewArray[$field->getName() . '-errors'] = array();
        }
        foreach ($this->errorMessages['field'] as $fieldName => $errors) {
            $viewArray[$fieldName . '-errors'] = $errors;
        }
        $viewArray['errors'] = $this->errorMessages['form'];
        $this->view->setVar($this->name, $viewArray);
    }
}
