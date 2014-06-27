<?php

class MW_Cmspro_Model_Mysql4_Newsbackup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('cmspro/newsbackup', 'news_id');
    }
	public function getMainTable(){
		return $this->getTable('cmspro/news_backup');
	}
    
	public function saveNewsCategory($new_id, $data){
		$condition = $this->_getWriteAdapter()->quoteInto('news_id = ?', $new_id);
        $this->_getWriteAdapter()->delete($this->getTable('news_category_backup'), $condition);

		if (!$data['news_category'])
		{
			$storeArray = array();
            $storeArray['news_id'] = $new_id;
            $storeArray['category_id'] = '0';
            //Zend_Debug::dump($storeArray);die;
            $this->_getWriteAdapter()->insert($this->getTable('news_category_backup'), $storeArray);
		}
		else
		{
			foreach ((array)$data['news_category'] as $store) {
				$storeArray = array();
				$storeArray['news_id'] = $new_id;
				$storeArray['category_id'] = $store;
				//Zend_Debug::dump($storeArray);die;
				$this->_getWriteAdapter()->insert($this->getTable('news_category_backup'), $storeArray);
			}
		}

    }
	
}