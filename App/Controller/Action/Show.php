<?php
class App_Controller_Action_Show extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
        $this->view->pictures = $pictureMapper->getTwoRandomPictures(mt_rand(0, 1));
    }

    public function top10Action() {
        $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
        $this->view->pictures = $pictureMapper->getTop10ByGender($this->getGenderToShow());
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
                default:
                    $genderToShow = mt_rand(0, 1);
                    break;
            }
        }
        return $genderToShow;
    }
}