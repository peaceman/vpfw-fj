<?php
class App_Controller_Action_Picture extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {

    }

    public function uploadAction() {
        $genderField = new Vpfw_Form_Field_Radio('gender', array('male', 'female'));
        $rightsField = new Vpfw_Form_Field('rights');
        $pictureField = new Vpfw_Form_Field_File('picture');

        $inArrayValidator = new Vpfw_Form_Validator_InArray(array('male', 'female'));

        $genderField->addValidator($inArrayValidator);

        $form = new Vpfw_Form($this->request, 'picupload', array($genderField, $rightsField, $pictureField), $this->view);
        $form->setAction(Vpfw_Router_Http::url('picture', 'upload'))
             ->setMethod('POST')
             ->setEnctype('multipart/form-data')
             ->handleRequest();
        
//        if (true == $form->formWasSent()) {
//            echo '<pre>';
//            var_dump($_FILES);
//            var_dump($form);
//            var_dump($this->request);
//            die();
//        }
        
        $form->fillView();
    }
}
