<?php
class Mage_Rating_Model_Rating extends Mage_Core_Model_Abstract{
	  public function __construct()
    {
        $this->_init('cmspro/rating');
    }
	
	 public function addOptionVote($optionId, $newsId)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setNewsId($newsId)
            ->addVote();
        return $this;
    }
	
	
	 public function updateOptionVote($optionId)
    {
        Mage::getModel('cmspro/rating_option')->setOptionId($optionId)
            ->setRatingId($this->getRatingId())
            ->setNewsId($this->getNewsId())
            ->setDoUpdate(1)
            ->addVote();
        return $this;
    }

	/**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($options = $this->getData('options')) {
            return $options;
        }
        elseif ($id = $this->getId()) {
            return Mage::getResourceModel('cmspro/rating_collection')
               ->addRatingFilter($id)
               ->setPositionOrder()
               ->load()
               ->getItems();
        }
        return array();
    }
}