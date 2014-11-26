<?php

class MW_Cmspro_Model_Categorystore extends Mage_Core_Model_Abstract
{
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/categorystore');
    }
	
	
	public function getRelatedProductCollection(){
		$collection = Mage::getModel('cmspro/category')
				->getCollection();
		return $collection;
	}
	
	
}