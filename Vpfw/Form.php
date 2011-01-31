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
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $enctype;


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
        $this->method = 'GET';
        $this->enctype = 'application/x-www-form-urlencoded';
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
    }

    public function addErrorForField($fieldName, $message) {
        $this->errorMessages['field'][$fieldName][] = $message;
    }

    public function addErrorForForm($message) {
        $this->errorMessages['form'][] = $message;
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

    public function handleRequest() {
        if (true == $this->formWasSent()) {
            $this->checkExistanceOfAllRequiredFields();
            $this->processSentFields();
        }
    }

    public function formWasSent() {
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
                $field->setValue($this->request->getParameter($field->getName()));
                if (false == $field->isFilled()) {
                    $this->errorMessages['form'][] = 'Das Feld ' . $field->getName() . ' muss zwingend ausgefüllt werden';
                    $this->allIsValid = false;
                }
            }
        }
    }

    private function processSentFields() {
        foreach ($this->fields as $field) {
            /* @var $field Vpfw_Form_Field */
            if (true == $field->isFilled()) {
                $validationResult = $field->executeFilters()
                                          ->executeValidators();
                if (true !== $validationResult) {
                    $this->errorMessages['field'][$field->getName()] = $validationResult;
                    $this->allIsValid = false;
                }
            }
        }
    }

    public function fillView() {
        $viewArray = array();
        foreach ($this->fields as $field) {
            $fieldViewArray = $field->fillView();
            $viewArray = array_merge($viewArray, $fieldViewArray);
            $viewArray[$field->getName() . '-errors'] = array();
        }
        foreach ($this->errorMessages['field'] as $fieldName => $errors) {
            $viewArray[$fieldName . '-errors'] = $errors;
        }
        $viewArray['errors'] = $this->errorMessages['form'];
        $viewArray['method'] = $this->method;
        $viewArray['action'] = $this->action;
        $viewArray['enctype'] = $this->enctype;
        $this->view->setVar($this->name, $viewArray);
    }

    /**
     *
     * @param string $action
     * @return Vpfw_Form
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    /**
     *
     * @param string $method
     * @return Vpfw_Form
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    /**
     *
     * @param string $enctype
     * @return Vpfw_Form
     */
    public function setEnctype($enctype) {
        switch($enctype) {
            case 'application/x-www-form-urlencoded':
            case 'multipart/form-data':
            case 'text/plain':
                $this->enctype = $enctype;
                break;
            default:
                throw new Vpfw_Exception_Logical('Unbekannter Formular-Enctype ' . $enctype);
        }
        return $this;
    }

    public function getEnctype() {
        return $this->enctype;
    }
}
