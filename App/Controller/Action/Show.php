<?php
class App_Controller_Action_Show extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        $pictureMapper = Vpfw_Factory::getDataMapper('Picture');
        $this->view->pictures = $pictureMapper->getTwoRandomPictures(mt_rand(0, 1));
        if (2 == count($this->view->pictures)) {
            $comparisonMapper = Vpfw_Factory::getDataMapper('PictureComparison');
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
            $comparisonDao = $comparisonMapper->getEntriesByFieldValue($searchArray);
            if (0 == count($comparisonDao)) {
                $comparisonDao = $comparisonMapper->createEntry(array('PictureId1' => $this->view->pictures[0]->getId(), 'PictureId2' => $this->view->pictures[1]->getId()), true);
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
            }
        }
        if (true == is_null($genderToShow)) {
            $genderToShow = mt_rand(0, 1);
        }
        return $genderToShow;
    }
}