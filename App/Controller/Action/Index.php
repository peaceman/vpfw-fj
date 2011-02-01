<?php
class App_Controller_Action_Index extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        $this->request->addActionControllerInfo(array('ControllerName' => 'Show'));
    }
}
