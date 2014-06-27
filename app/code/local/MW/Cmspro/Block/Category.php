<?php
class MW_Cmspro_Block_Category extends Mage_Core_Block_Template
{
    
    /*public function getsMainCategories(){
    	$categories = Mage::getModel('cmspro/category')->getCollection()
    				->addFieldToFilter('category_id','1')
    				->addFieldToFilter('status','1');
		return $categories;
    }*/
    
    public function _getRootURL(){
    	return Mage::getModel('cmspro/category')->load(1)->_getUrlRewrite();
    }
    
    public function _getRootName(){
    	return Mage::getModel('cmspro/category')->load(1)->getName();
    }
}