<?php
class App_Controller_Action_Show extends Vpfw_Controller_Action_Abstract {
    private function getTwoPictures() {
        $pictures = array();
        if ($this->request->issetParameter('comparisonId')) {
            try {
                $this->comparisonMapper->getEntryById($this->request->getParameter('comparisonId'));
            } catch (Vpfw_Exception_OutOfRange $e) {
                $this->view->addErrorMessage('Ein Bildvergleich mit der Id ' . $this->request->getParameter('comparisonId') . ' existiert nicht.');
            }
        }
        try {
            if ($this->request->issetParameter('comparisonId')) {
                $this->comparisonMapper->getEntryById($this->request->getParameter('comparisonId'));
            }
        } catch (Vpfw_Exception_OutOfRange $e) {

        }
    }

    public function indexAction() {
        $this->needDataMapper('Picture');
        $this->needDataMapper('Comparison');
        $this->view->pictures = $this->getTwoPictures();
        $this->view->pictures = $this->pictureMapper->getTwoRandomPictures(mt_rand(0, 1));
        if (2 == count($this->view->pictures)) {
            $searchArray = array(
                array(
                    'i|PictureId1|' . $this->view->pictures[0]->getId(),
                    'i|PictureId2|' . $this->view->pictures[1]->getId(),
                ),
                array(
                    'i|PictureId1|' . $this->view->pictures[1]->getId(),
                    'i|PictureId2|' . $this->view->pictures[0]->getId(),
                )
            );
            $comparisonDao = $this->comparisonMapper->getEntriesByFieldValue($searchArray);
            if (0 == count($comparisonDao)) {
                $comparisonDao = $this->comparisonMapper->createEntry(array('PictureId1' => $this->view->pictures[0]->getId(), 'PictureId2' => $this->view->pictures[1]->getId()), true);
            } else {
                $comparisonDao = $comparisonDao[0];
            }
            $i = 1;
            foreach ($this->view->pictures as $picture) {
                $picture->increaseSiteHits();
                /* @var $request Vpfw_Request_Interface */
                $request = clone $this->request;
                $request->setParameter('pId', $picture->getId());
                $request->setParameter('cId', $comparisonDao->getId());
                $actionController = Vpfw_Factory::getActionController('picture', 'addComment', null, array('request' => $request));
                $this->addChildController('commentForm' . $i, $actionController);
                $i++;
            }
            $this->view->comparisonId = $comparisonDao->getId();
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