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
        
        if (true == $form->formWasSent() && true == $form->isAllValid()) {
            $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
            $pictureDao = $pictureMapper->createEntry();
            /* @var $pictureDao App_DataObject_Picture */
            $validValues = $form->getValidValues();
            $validValues['Md5'] = md5_file($validValues['picture']['tmp_name']);
            $validValues['SessionId'] = $this->session->getSession()->getId();
            $validValues['UploadTime'] = time();
            $validValues['DeletionId'] = null;
            unset($validValues['rights'], $validValues['picture']);
            $validationResult = $pictureDao->publicate($validValues);
            if (true === $validationResult) {
                try {
                    $im = new Imagick($_FILES['picture']['tmp_name']);
                    $im->setImageFormat('jpg');
                    $im->writeImage('pics/' . $validValues['Md5'] . '.jpg');
                    if (true == $this->session->isLoggedIn()) {
                        $this->response->addHeader('Location', Vpfw_Router_Http::url('user', 'uploadedPictures'));
                    } else {
                        $this->response->addHeader('Location', Vpfw_Router_Http::url('show', 'index'));
                    }
                } catch (ImagickException $e) {
                    $form->addErrorForField('picture', $e->getMessage());
                    $pictureDao->notifyObserver();
                }
            } else {
                foreach ($validationResult as $error) {
                    $form->addErrorForForm($error->getMessage());
                }
                $pictureDao->notifyObserver();
            }
        }
        
        $form->fillView();
    }

    public function rateAction() {
        if (false == $this->request->issetParameter('cId') ||
            false == $this->request->issetParameter('pId') ||
            false == $this->request->issetParameter('rating')) {
            $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
        } else {
            $rating = $this->request->getParameter('rating');
            if ('positive' !== $rating && 'negative' !== $rating) {
                $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
            } else {
                $comparisonMapper = Vpfw_Factory::getDataMapper('PictureComparison');
                $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
                $comparisonDao = null;
                try {
                    $comparisonDao = $comparisonMapper->getEntryById($this->request->getParameter('cId'));
                } catch (Vpfw_Exception_OutOfRange $e) {
                    $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
                }
                if (false == is_null($comparisonDao)) {
                    if ('positive' == $rating) {
                        $comparisonDao->setWinnerByPictureId($this->request->getParameter('pId'));
                    } else {
                        $comparisonDao->setLoserByPictureId($this->request->getParameter('pId'));
                    }
                    $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
                }
            }
        }
    }
}
