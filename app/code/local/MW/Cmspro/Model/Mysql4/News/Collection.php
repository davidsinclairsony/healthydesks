<?php

class MW_Cmspro_Model_Mysql4_News_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/news');
    }
	/**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        //Added this code - START  -------------------->    
        if (Mage::getSingleton('adminhtml/url')->getRequest()->getModuleName() == 'cmspro') {
            $countSelect->reset(Zend_Db_Select::GROUP);
        }
        //Added this code - END -----------------------<
        /*$countSelect->from('', 'COUNT(DISTINCT e.entity_id)');
        $countSelect->resetJoinLeft();*/
		$countSelect->columns('COUNT(*)');
        //echo $countSelect;die;
		return $countSelect;
    }

    /**
     * Adds filter by status
     *
     * @param int $status
     * @return MW_Cmspro_Model_Mysql4_News_Collection
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    
    /**
     * Adds filter by restriction
     *
     * @param int $status
     * @return MW_Cmspro_Model_Mysql4_News_Collection
     */
    public function addRestrictionFilter($cig,$cid)
    {
    	if(isset($cid)){
	        $this->getSelect()				
			->where('(main_table.users like ?) or ((main_table.groups =",all," or main_table.groups like "'.'%,'.$cig.',%"'.') and main_table.users="")', "%,".$cid.",%");
    	}
		else{
			$this->getSelect()	
			->where('(main_table.groups like ? or main_table.groups =",all,") and main_table.users=""', "%,".$cig.",%");						
		}; 
    }    
}