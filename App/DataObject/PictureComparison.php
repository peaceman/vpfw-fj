<?php
class App_DataObject_PictureComparison extends Vpfw_DataObject_Abstract {
    /**
     * @var App_DataMapper_Picture
     */
    private $pictureMapper;

    /*
     * @var App_DataObject_Picture[]
     */
    private $pictureArray = array();

    public function __construct(App_DataMapper_Picture $pictureMapper, $properties = null) {
        $this->pictureMapper = $pictureMapper;
        $this->pictureArray[1] = null;
        $this->pictureArray[2] = null;
        $this->data = array(
            'Id' => null,
            'PictureId1' => null,
            'PictureId2' => null,
        );
        foreach ($this->data as &$val) {
            $val = array('val' => null, 'changed' => false, 'required' => true);
        }
        parent::__construct($properties);
    }

    public function getPictureId1() {
        if (true == is_object($this->pictureArray[1])) {
            return $this->pictureArray[1]->getId();
        } else {
            return $this->getData('PictureId1');
        }
    }

    public function getPicture1() {
        if (true == is_null($this->pictureArray[1])) {
            $this->pictureArray[1] = $this->pictureMapper->getEntryById($this->getPictureId1());
        }
        return $this->pictureArray[1];
    }

    public function getPictureId2() {
        if (true == is_object($this->pictureArray[2])) {
            return $this->pictureArray[2]->getId();
        } else {
            return $this->getData('PictureId2');
        }
    }

    public function getPicture2() {
        if (true == is_null($this->pictureArray[2])) {
            $this->pictureArray[2] = $this->pictureMapper->getEntryById($this->getPictureId2());
        }
        return $this->pictureArray[2];
    }

    public function setPictureId1($id, $validation = true) {
        if ($this->getPictureId1() != $id) {
            $this->setData('PictureId1', $id);
        }
    }

    public function setPicture1($picture) {
        $this->pictureArray[1] = $picture;
        if (true == is_object($picture)) {
            if ($this->getPictureId1() != $picture->getId()) {
                $this->setData('PictureId1', $picture->getId());
            }
        }
    }

    public function setPictureId2($id, $validation = true) {
        if ($this->getPictureId2() != $id) {
            $this->setData('PictureId2', $id);
        }
    }

    public function setPicture2($picture) {
        $this->pictureArray[2] = $picture;
        if (true == is_object($picture)) {
            if ($this->getPictureId2() != $picture->getId()) {
                $this->setData('PictureId2', $picture->getId());
            }
        }
    }

    public function setWinnerByPictureId($picId) {
        $winner = null;
        $loser = null;
        if ($picId == $this->getPictureId1()) {
            $winner = $this->getPicture1();
            $loser = $this->getPicture2();
        } elseif ($picId == $this->getPictureId2()) {
            $winner = $this->getPicture2();
            $loser = $this->getPicture1();
        } else {
            return;
        }
        $winner->increaseRating();
        $loser->decreaseRating();
    }

    public function setLoserByPictureId($picId) {
        $winner = null;
        $loser = null;
        if ($picId == $this->getPictureId1()) {
            $loser = $this->getPicture1();
            $winner = $this->getPicture2();
        } elseif ($picId == $this->getPictureId2()) {
            $loser = $this->getPicture2();
            $winner = $this->getPicture1();
        } else {
            return;
        }
        $winner->increaseRating();
        $loser->decreaseRating();
    }
}
