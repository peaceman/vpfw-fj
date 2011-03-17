<?php
class App_Controller_Action_Admin extends Vpfw_Controller_Action_Abstract {
    public function __construct() {
        $this->needDataMapper('RuleViolation');
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

    }

    public function picturesAction() {

    }
}
