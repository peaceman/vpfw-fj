<?php
class App_Controller_Action_User extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        
    }

    public function loginAction() {
        $usernameField = new Vpfw_Form_Field('username');
        $passwordField = new Vpfw_Form_Field('password');

        $notEmptyValidator = new Vpfw_Form_Validator_NotEmpty();
        $lengthValidator = new Vpfw_Form_Validator_Length(3, 32);
        $lengt2Validator = new Vpfw_Form_Validator_Length(3);
        $trimSpacesFilter = new Vpfw_Form_Filter_TrimSpaces();

        $usernameField->addFilter($trimSpacesFilter);
        $usernameField->setValidators(array($notEmptyValidator, $lengthValidator));

        $passwordField->addFilter($trimSpacesFilter);
        $passwordField->setValidators(array($notEmptyValidator, $lengt2Validator));

        $form = new Vpfw_Form($this->request, 'login', array($usernameField, $passwordField), $this->view);
        $form->setAction(Vpfw_Router_Http::url('user', 'login'));
        $form->setMethod('POST');
        $form->handleRequest();
        $form->fillView();
    }

    public function registerAction() {
        $usernameField = new Vpfw_Form_Field('username');
        $passwordField = new Vpfw_Form_Field('password');
        $emailField = new Vpfw_Form_Field('email');

        $notEmptyValidator = new Vpfw_Form_Validator_NotEmpty();
        $lengthValidator = new Vpfw_Form_Validator_Length(3, 32);
        $length2Validator = new Vpfw_Form_Validator_Length(3);
        $length3Validator = new Vpfw_Form_Validator_Length(null, 128);
        $emailValidator = new Vpfw_Form_Validator_Email();
        $trimSpaceFilter = new Vpfw_Form_Filter_TrimSpaces();

        $usernameField->addFilter($trimSpaceFilter);
        $usernameField->addValidator($lengthValidator)->addValidator($notEmptyValidator);

        $passwordField->addFilter($trimSpaceFilter);
        $passwordField->setValidators(array($notEmptyValidator, $length2Validator));

        $emailField->addFilter($trimSpaceFilter);
        $emailField->setValidators(array($notEmptyValidator, $length3Validator, $emailValidator));

        $form = new Vpfw_Form($this->request, 'register', array($usernameField, $passwordField, $emailField), $this->view);
        $form->setAction(Vpfw_Router_Http::url('user', 'register'));
        $form->setMethod('POST');
        $form->handleRequest();

        if (true == $form->formWasSent() && true == $form->isAllValid()) {
            $userMapper = Vpfw_Factory::getDataMapper('User');
            $userDao = $userMapper->createEntry();
            /* @var $userDao App_DataObject_User */
            $validValues = $form->getValidValues();
            $validValues['passhash'] = md5($validValues['password']);
            unset($validValues['password']);
            $validValues['CreationIp'] = $this->request->getRemoteAddress();
            $validValues['CreationTime'] = time();
            $validValues['DeletionId'] = null;
            $validationResult = $userDao->publicate($validValues);
            if (true === $validationResult) {
                $this->response->addHeader('Location', Vpfw_Router_Http::url('User', 'login'));
            } else {
                foreach ($validationResult as $error) {
                    $form->addErrorForForm($error->getMessage());
                }
                $userDao->notifyObserver();
            }
        }

        $form->fillView();
    }
}