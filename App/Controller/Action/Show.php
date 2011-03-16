<?php
class App_Controller_Action_Show extends Vpfw_Controller_Action_Abstract {
    public function __construct() {
        parent::__construct();
        $this->needDataMapper('Picture');
        $this->needDataMapper('PictureComparison');
    }

    private function getComparison() {
        $comparison = null;
        if ($this->request->issetParameter('comparisonId')) {
            $comparison = $this->getComparisonById($this->request->getParameter('comparisonId'));
        } else {
            $comparison = $this->getRandomComparison($this->session->get('genderToRate'));
        }
        return $comparison;
    }

    private function getComparisonById($id) {
        try {
            $comparison = $this->picturecomparisonMapper->getEntryById($id);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Bildvergleich mit der Id ' . HE($id, false) . ' existiert nicht.');
            $this->interruptExecution();
        }
        return $comparison;
    }

    private function getRandomComparison($gender = null) {
        if (is_null($gender) || $gender == 'random') {
            $gender = mt_rand(0, 1);
        }
        $pictures = $this->pictureMapper->getTwoRandomPictures($gender);
        if (count($pictures) !=  2) {
            $this->view->setContent('Es konnten keine 2 Bilder ermittelt werden, wahrscheinlich existieren noch nicht genügend Bilder in der Datenbank');
            $this->interruptExecution();
        }
        $comparison = $this->picturecomparisonMapper->getComparisonByPictureIds($pictures[0]->getId(), $pictures[1]->getId());
        return $comparison;
    }

    public function indexAction() {
        $comparison = $this->getComparison();
        $pictures = $comparison->getPictures();

        $this->addChildController('selectGender', array('show', 'selectGender'));

        $this->view->pictures = $pictures;
        $i = 1;
        foreach ($pictures as $picture) {
            $picture->increaseSiteHits();
            /* @var $request Vpfw_Request_Interface */
            $request = clone $this->request;
            $request->setParameter('commentedPictureId', $picture->getId());
            $request->setParameter('comparisonId', $comparison->getId());
            $actionController = Vpfw_Factory::getActionController('picture', 'addComment', null, array('request' => $request));
            $this->addChildController('commentForm' . $i, $actionController);
            $i++;
        }
        $this->view->comparisonId = $comparison->getId();
    }

    public function selectGenderAction() {
        $genderSelectField = new Vpfw_Form_Field_MultipleChoice('gender', array('random', 'male', 'female'));
        $inArrayValidator = new Vpfw_Form_Validator_InArray(array('random', 'male', 'female'));
        $genderSelectField->addValidator($inArrayValidator);
        if (!is_null($this->session->get('genderToRate'))) {
            $genderSelectField->setValue($this->session->get('genderToRate'));
        }
        $form = new Vpfw_Form($this->request, 'genderSelect', array($genderSelectField));
        $form->setAction(Vpfw_Router_Http::url('show', 'selectGender'))
             ->setMethod('post')
             ->handleRequest();
        $this->view->form = $form;

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $this->session->set('genderToRate', $validValues['gender']);
            $this->response->addHeader('Location', Vpfw_Router_Http::url('show'));
        } elseif ($form->formWasSent()) {
            $this->view->setContent('Ungültige Auswahl');
            $this->interruptExecution();
        }
    }

    public function top10Action() {
        $this->needDataMapper('Picture');
        $this->view->pictures = $this->pictureMapper->getTop10ByGender($this->getGenderToShow());
    }

    private function getGenderToShow() {
        $genderToShow = null;
        if (true == $this->request->issetParameter('gender')) {
            switch ($this->request->getParameter('gender')) {
                case 'male':
                    $genderToShow = 0;
                    break;
                case 'female':
                    $genderToShow = 1;
                    break;
            }
        }
        if (true == is_null($genderToShow)) {
            $genderToShow = mt_rand(0, 1);
        }
        return $genderToShow;
    }
}