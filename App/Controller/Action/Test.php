<?php
class App_Controller_Action_Test extends Vpfw_Controller_Action_Abstract {
    protected function indexAction() {
        $usernameField = new Vpfw_Form_Field('username');
        $usernameField->addValidator(new Vpfw_Form_Validator_Length(5, 6));
        $passwordField = new Vpfw_Form_Field('password');
        $passwordField->addValidator(new Vpfw_Form_Validator_Length(2, 5))
                      ->addValidator(new Vpfw_Form_Validator_NotEmpty(), Vpfw_Form_Field::FRONT)
                      ->addFilter(new Vpfw_Form_Filter_TrimSpaces());
        $fields = array(
            $usernameField,
            $passwordField
        );
        $form = new Vpfw_Form($this->request, 'master', $fields, $this->view);
    }
}
