<?php

class MW_Cmspro_Model_Mysql4_Newsnews extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/newsnews', 'id');
    }
	
	
	public function getMainTable(){
		return $this->getTable('cmspro/news_news')	;
	}
	
	
	public function processRelations($parentId, $news)
    {
    	//get from db $old array that hold news_news relation of editing news
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('related_news_id','position'))
            ->where('news_id=?', $parentId);
        $relation = $this->_getReadAdapter()->fetchAll($select);
        $old = array();
        foreach ($relation as $rel)
        {
        	$old[$rel['related_news_id']] = $rel['position'];
        }
        //Zend_Debug::dump($relation);
		//Zend_Debug::dump($news);
        //Zend_Debug::dump($old);die;

        $insert = array_diff_assoc($news, $old);
        $delete = array_diff_assoc($old, $news);
		//Zend_Debug::dump($insert);
        //Zend_Debug::dump($delete);die;
        //Zend_Debug::dump(array_keys($delete));die;
    	if (!empty($delete)) {
            $where = join(' AND ', array(
                $this->_getWriteAdapter()->quoteInto('news_id=?', $parentId),
                $this->_getWriteAdapter()->quoteInto( 'related_news_id IN(?)', array_keys($delete) )
            ));
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
        
        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $key=>$val) {
                $insertData[] = array(
                    'news_id' => $parentId,
                    'related_news_id'  => $key,
                	'position'	 => $val,
                );
            }
            //Zend_Debug::dump($insertData);die;
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $insertData);
        }

        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object){
		/*$condition = $this->_getWriteAdapter()->quoteInto('new_id = ?', $parentId);
       $this->_getWriteAdapter()->delete($this->getTable('cmspro/relation'), $condition);

		if (!$object)
		{
			$storeArray = array();
			
            $storeArray['new_id'] = $object->getId();
            $storeArray['entity_id'] = '0';
			$storeArray['position'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('cmspro/relation'), $storeArray);
		}
		else
		{
		
			 $obj = $object->getData();
			
			$total  = count($obj['entity_id']);
			
			for($i = 0 ; $i < $total; $i++){
				$storeArray = array();
				
				
					$storeArray['entity_id'] = $obj['entity_id'][$i];
					$storeArray['position'] = $obj['position'][$i];
					$storeArray['new_id'] = $object->getId();
			
				$this->_getWriteAdapter()->insert($this->getTable('cmspro/relation'), $storeArray);
				
			}
			
		}

        return parent::_afterSave($object);*/
    }
	
    
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('cmspro/news_news'), 'news_id='.$object->getId());

	}
	
}