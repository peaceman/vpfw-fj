<?php
class App_Controller_Action_Picture extends Vpfw_Controller_Action_Abstract {
    public function __construct($environment = null) {
        parent::__construct($environment);
        $this->needDataMapper('PictureComparison');
        $this->needDataMapper('PictureComment');
        $this->needDataMapper('Picture');
    }

    public function indexAction() {

    }

    public function showAction() {
        if (!$this->request->issetParameter('pId')) {
            $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
        } else {
            try {
                $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
                $picture = $pictureMapper->getEntryById($this->request->getParameter('pId'));
                $this->view->picture = $picture;
            } catch (Vpfw_Exception_OutOfRange $e) {
                $this->response->setStatus('404');
                $this->response->setBody('404 Not Found');
                $this->disableViewRendering();
            }
        }
    }
    
    private function getPictureFromRequestData() {
        $pictureId = (int)$this->request->getParameter('commentedPictureId');
        $picture = null;
        try {
            $picture = $this->pictureMapper->getEntryById($pictureId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Bild mit der Id ' . HE($pictureId, false) . ' existiert nicht');
            throw new Vpfw_Exception_Interrupt();
        }
        return $picture;
    }
    
    private function getComparisonFromRequestData() {
        $comparisonId = (int)$this->request->getParameter('comparisonId');
        $comparison = null;
        try {
            $comparison = $this->picturecomparisonMapper->getEntryById($comparisonId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Bildvergleich mit der Id ' . HE($comparisonId, false) . ' existiert nicht');
            throw new Vpfw_Exception_Interrupt();
        }
        return $comparison;
    }

    public function addCommentAction() {
        $picture = $this->getPictureFromRequestData();
        $comparison = $this->getComparisonFromRequestData();

        $commentField = new Vpfw_Form_Field('text');
        $commentField->addFilter(new Vpfw_Form_Filter_TrimSpaces())
                ->addValidator(new Vpfw_Form_Validator_NotEmpty())
                ->addValidator(new Vpfw_Form_Validator_Length(3, 255));

        $formAction = Vpfw_Router_Http::url('picture', 'addComment', array(
            'commentedPictureId' => $picture->getId(),
            'comparisonId' => $comparison->getId())
        );
        $form = new Vpfw_Form($this->request, 'piccomment' . $picture->getId(), array($commentField));
        $form->setAction($formAction)->setMethod('post')->handleRequest();
        $this->view->setVar('form', $form);
        if ($form->formWasSent() && $form->isAllValid()) {
            $pictureComment = $this->picturecommentMapper->createEntry();

            $validValues = $form->getValidValues();
            $validValues['SessionId'] = $this->session->getSession()->getId();
            $validValues['Time'] = time();
            $validValues['PictureId'] = $picture->getId();

            $validationResult = $pictureComment->publicate($validValues);
            if (true === $validationResult) {
                $nextLocation = Vpfw_Router_Http::url('picture', 'show', array('pictureId' => $picture->getId()));
                $this->response->addHeader('Location', $nextLocation);
            } else {
                $pictureComment->notifyObserver();
                $this->request->setParameter('commentFormErrors', array('commentedPictureId' => $picture->getId(), 'errors' => $validationResult));
                $this->request->addActionControllerInfo(array('ControllerName' => 'show'));
            }
        } elseif ($form->formWasSent()) {
            $this->request->addActionControllerInfo(array('ControllerName' => 'show'));
        }
    }

//    public function addCommentAction() {
//        $pictureId = (int)$this->request->getParameter('commentedPictureId');
//        $comparisonId = (int)$this->request->getParameter('comparisonId');
//        $whitespaceFilter = new Vpfw_Form_Filter_TrimSpaces();
//        $notEmptyValidator = new Vpfw_Form_Validator_NotEmpty();
//        $lengthValidator = new Vpfw_Form_Validator_Length(3, 255);
//        $comment = new Vpfw_Form_Field('text');
//        $comment->addFilter($whitespaceFilter);
//        $comment->setValidators(array($notEmptyValidator, $lengthValidator));
//        $form = new Vpfw_Form($this->request, 'piccomment', array($comment), $this->view);
//        $formAction = Vpfw_Router_Http::url('picture', 'addComment', array('commentedPictureId' => $pictureId, 'comparisonId' => $comparisonId));
//        $form->setAction($formAction)
//             ->setMethod('post')
//             ->handleRequest();
//
//        if ($form->formWasSent() && $form->isAllValid()) {
//            /* @var $pictureMapper Vpfw_DataMapper_Picture */
//            $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
//            /* @var $pictureCommentMapper Vpfw_DataMapper_PictureComment */
//            $pictureCommentMapper = Vpfw_Factory::getDataMapper('PictureComment');
//            try {
//                /* @var $picture App_DataObject_Picture */
//                $picture = $pictureMapper->getEntryById($pictureId);
//                /* @var $pictureComment App_DataObject_PictureComment */
//                $pictureComment = $pictureCommentMapper->createEntry();
//
//                $validValues = $form->getValidValues();
//                $validValues['SessionId'] = $this->session->getSession()->getId();
//                $validValues['Time'] = time();
//                $validValues['PictureId'] = $picture->getId();
//
//                $validationResult = $pictureComment->publicate($validValues);
//                if (true === $validationResult) {
//                    $this->response->addHeader('Location', Vpfw_Router_Http::url('picture', 'show', array('pId' => $picture->getId())));
//                } else {
//                    foreach ($validationResult as $error) {
//                        $form->addErrorForForm($error->getMessage());
//                    }
//                    $pictureComment->notifyObserver();
//                }
//            } catch (Vpfw_Exception_OutOfRange $e) {
//                $this->request->addActionControllerInfo(array('ControllerName' => 'index'));
//            }
//        }
//        $form->fillView();
//    }

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
