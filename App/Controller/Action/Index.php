<?php
class App_Controller_Action_Index extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
        $this->view->pictures = $pictureMapper->getTwoRandomPictures(mt_rand(0, 1));
    }
}
