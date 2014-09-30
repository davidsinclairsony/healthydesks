<?php

class MW_Cmspro_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/category');
    }
   /**
     * Enter description here...
     *
     * @param array $categoryIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    
    //public function setData(){}
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $categoryIds);
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('category_id', $condition);
        return $this;
    }
	
	public function getMainCategories123(){
		//$categories = $this->addFieldToFilter()->
	}
}