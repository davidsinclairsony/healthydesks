<?php

class MW_Cmspro_Model_Mysql4_News extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/news', 'news_id');
    }
    
   /* protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('news_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('news_category'), $condition);

		if (!$object->getData('news_category'))
		{
			$storeArray = array();
            $storeArray['news_id'] = $object->getId();
            $storeArray['category_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('news_category'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('news_category') as $store) {
				$storeArray = array();
				$storeArray['news_id'] = $object->getId();
				$storeArray['category_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('news_category'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }*/
    
	public function saveNewsCategory(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('news_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('news_category'), $condition);

		if (!$object->getData('news_category'))
		{
			$storeArray = array();
            $storeArray['news_id'] = $object->getId();
            $storeArray['category_id'] = '0';
            //Zend_Debug::dump($storeArray);die;
            $this->_getWriteAdapter()->insert($this->getTable('news_category'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('news_category') as $store) {
				$storeArray = array();
				$storeArray['news_id'] = $object->getId();
				$storeArray['category_id'] = $store;
				//Zend_Debug::dump($storeArray);die;
				$this->_getWriteAdapter()->insert($this->getTable('news_category'), $storeArray);
			}
		}

    }
    public function test()
    {
    	/*$adapter = $this->_getReadAdapter();
    	$adapter->delete($this->getTable('relation'), 'new_id='.$object->getId());
		$adapter->delete($this->getTable('cmspro/news_store'), 'news_id='.$object->getId());
		$adapter->delete($this->getTable('core/url_rewrite'), 'target_path = cmspro/view/details/id/'.$object->getId());*/
    }
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('cmspro/news_category'), 'news_id='.$object->getId());
		$adapter->delete($this->getTable('relation'), 'new_id='.$object->getId());
		$adapter->delete($this->getTable('cmspro/news_store'), 'news_id='.$object->getId());
		$adapter->delete($this->getTable('core/url_rewrite'), 'target_path = "cmspro/view/details/id/'.$object->getId().'"');
		$adapter->delete($this->getTable('cmspro/comment'), 'news_id = '.$object->getId());
		
		// delete news backup
		$adapter->delete($this->getTable('cmspro/news_backup'), 'news_id_parent='.$object->getId());
		
		//decrement popularity
		//$writeAdapter = $this->_getReadAdapter();
		$tagsId = $adapter->fetchCol('SELECT DISTINCT tag_id FROM '.$this->getTable('cmspro/news_tag').' WHERE news_id = '.$object->getId());
//		var_dump($tagsId);die;
		$tagResourceModel =  Mage::getResourceModel('cmspro/tag');
		$tagResourceModel->decrementPopularity($tagsId);		
	}
	
}