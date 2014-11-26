<?php
class MW_Cmspro_Model_Tag extends Mage_Core_Model_Abstract
{
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;
	
	protected function _construct()
	{
		parent::_construct();
		$this->_init('cmspro/tag');
	}
	
    public function getTagId()
    {
        return $this->_getData('tag_id');
    }
	
    public function getRatio()
    {
        return $this->_getData('ratio');
    }

    public function setRatio($ratio)
    {
        $this->setData('ratio', $ratio);
        return $this;
    }	
	
	/**
     * Retrieves array of related news IDs
     *
     * @return array
     */
    public function getRelatedNewIds()
    {
    	return Mage::getModel('cmspro/newstag')
            ->setTagId($this->getTagId())
            ->setStatusFilter($this->getStatusFilter())
            ->getNewsIds();
    	
    }
    
    public function getTaggedNewsUrl()
    {
        return Mage::getUrl('cmspro/tag/list', array('tagId' => $this->getTagId()));
    }    
    
    /**
     * Product delete event action
     *
     * @param  Varien_Event_Observer $observer
     * @return MW_Cmspro_Model_Tag
     */
    public function newsDeleteEventAction($observer)
    {
        $this->_getResource()->decrementPopularity($this->_getProductEventTagsCollection($observer)->getAllIds());
        return $this;
    }
    
    public function loadByName($name)
    {
        $this->_getResource()->loadByName($this, $name);
        return $this;
    }
    
}