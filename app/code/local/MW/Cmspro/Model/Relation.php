<?php

class MW_Cmspro_Model_Relation extends Mage_Core_Model_Abstract
{
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/relation');
    }
	
	
	public function getRelatedProductCollection(){
		$collection = Mage::getModel('cmspro/relation')
				->getCollection();
		return $collection;
	}
	
	public function setRelatedProducts($newId, $arr){
	
		//prepare form data as $new array that hold editing relation data
		$model = Mage::getResourceModel('cmspro/relation');
		$new = array();
		foreach ($arr as $key=>$val)
		{
			$new[$key] = $val['position'];
			
		}
		
		if($newId  == 0){
			$newId = 1;
		}
		 
		//$productIds = array_keys($arr);
		$model->processRelations($newId, $new);
		//$model->setData($data)->setId($newId);
		//$model->save();
	}
	
}