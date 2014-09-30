<?php
class MW_Cmspro_Model_Rating_Option extends Varien_Object
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getResource()
    {
        return Mage::getResourceModel('cmspro/news_rating_option');
    }

    public function getResourceCollection()
    {
        return Mage::getResourceModel('rating/news_rating_option_collection');
    }

    public function load($optionId)
    {
        $this->setData($this->getResource()->load($optionId));
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }

    public function getId()
    {
        return $this->getOptionId();
    }

    public function getCollection()
    {
        return Mage::getResourceModel('rating/rating_option_collection');
    }
}
