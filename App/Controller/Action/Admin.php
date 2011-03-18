<?php
class App_Controller_Action_Admin extends Vpfw_Controller_Action_Abstract {
    public function __construct($environment = null) {
        parent::__construct($environment);
        $this->needDataMapper('RuleViolation');
        $this->needDataMapper('User');
    }

    public function indexAction() {
        $linksArray = array(
            array(
                'url' => Vpfw_Router_Http::url('admin', 'ruleviolations'),
                'name' => 'Regelverstöße',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'users'),
                'name' => 'Benutzer',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'pictures'),
                'name' => 'Bilder',
            )
        );
        $this->view->links = $linksArray;
    }

    public function pictureAction() {
        $picture = $this->getPictureFromRequestData();
        $this->view->picture = $picture;
        $this->view->user = $picture->getUser();
    }

    private function getPictureFromRequestData() {
        $pictureId = (int)$this->request->getParameter('pictureId');
        $picture = null;
        try {
            $picture = $this->pictureMapper->getEntryById($pictureId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->setContent('Ein Bild mit der Id ' . HE($pictureId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $picture;
    }

    public function ruleviolationsAction() {
        $this->view->unhandledRuleViolations = $this->ruleviolationMapper->getUnhandledRuleViolations();
        $this->view->handledRuleViolations = $this->ruleviolationMapper->getHandledRuleViolations();
    }

    public function ruleViolationAction() {
        $ruleViolation = $this->getRuleViolationFromRequestData();
        $handledState = $this->getHandledStateFromRequestData();
        $ruleViolation->setHandled($handledState);
        if ($handledState == 0)
            $this->view->undoUrl = Vpfw_Router_Http::url('admin', 'ruleViolation', array('ruleViolationId' => $ruleViolation->getId(), 'handledState' => 1));
        else
            $this->view->undoUrl = Vpfw_Router_Http::url('admin', 'ruleViolation', array('ruleViolationId' => $ruleViolation->getId(), 'handledState' => 0));
        $this->view->handledState = $handledState;
    }

    private function getRuleViolationFromRequestData() {
        $ruleViolationId = (int)$this->request->getParameter('ruleViolationId');
        $ruleViolation = null;
        try {
            $ruleViolation = $this->ruleviolationMapper->getEntryById($ruleViolationId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Regelverstoß mit der Id ' . HE($ruleViolationId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $ruleViolation;
    }

    private function getHandledStateFromRequestData() {
        $handledState = $this->request->getParameter('handledState');
        if (is_null($handledState) || ($handledState != 0 && $handledState != 1)) {
        $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'ruleviolations'));
            $this->interruptExecution();
        }
        return $handledState;
    }

    public function usersAction() {
        $this->view->users = $this->userMapper->getAllEntries();
    }

    public function userAction() {
        $user = $this->getUserFromRequestData();
        $pictures = $user->getPictures();
        $comments = $user->getPictureComments();
        $this->view->user = $user;
        $this->view->pictures = $pictures;
        $this->view->comments = $comments;
    }

    public function userDelAction() {
        $user = $this->getUserFromRequestData();

        $reasonField = new Vpfw_Form_Field('Reason', false);
        $whitespaceFilter = new Vpfw_Form_Filter_TrimSpaces();
        $notEmptyValidator = new Vpfw_Form_Validator_NotEmpty();
        $reasonField->addValidator($notEmptyValidator)
                ->addFilter($whitespaceFilter);

        $form = new Vpfw_Form($this->request, 'delUserProof', $reasonField);
        $form->setAction(Vpfw_Router_Http::url('admin', 'userDel', array('userId' => $user->getId())))
                ->setMethod('post')
                ->handleRequest();

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $parameters = array(
                'SessionId' => $this->session->getSession()->getId(),
                'Time' => time(),
            );
            if (array_key_exists('Reason', $validValues)) {
                $parameters['Reason'] = $validValues['Reason'];
            }
            $deletion = $this->deletionMapper->createEntry($parameters, true);

            $user->setDeletion($deletion);
        }
    }

    private function getUserFromRequestData() {
        $userId = (int)$this->request->getParameter('userId');
        $user = null;
        try {
            $user = $this->userMapper->getEntryById($userId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Benutzer mit der Id ' . HE($userId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $user;
    }

    public function picturesAction() {

    }
}
