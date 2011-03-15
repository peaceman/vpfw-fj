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
            $comparison = $this->getRandomComparison();
        }
        return $comparison;
    }

    private function getComparisonById($id) {
        try {
            $comparison = $this->picturecomparisonMapper->getEntryById($id);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Bildvergleich mit der Id ' . HE($id, false) . ' existiert nicht.');
            throw new Vpfw_Exception_Interrupt();
        }
        return $comparison;
    }

    private function getRandomComparison() {
        $pictures = $this->pictureMapper->getTwoRandomPictures(mt_rand(0, 1));
        $comparison = $this->picturecomparisonMapper->getComparisonByPictureIds($pictures[0]->getId(), $pictures[1]->getId());
        return $comparison;
    }

    public function indexAction() {
        $comparison = $this->getComparison();
        $this->view->pictures = $comparison->getPictures();
        $i = 1;
        foreach ($this->view->pictures as $picture) {
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