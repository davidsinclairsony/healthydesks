<?php

class MW_Cmspro_Model_Mysql4_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/relation', 'relation_id');
    }
	
	
	public function getMainTable(){
		return $this->getTable('cmspro/relation')	;
	}
	
	
	public function processRelations($parentId, $new)
    {
    	//get from db $old array that hold news_product relation of editing news
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('entity_id','position'))
            ->where('new_id=?', $parentId);
        $relation = $this->_getReadAdapter()->fetchAll($select);
        $old = array();
        foreach ($relation as $rel)
        {
        	$old[$rel['entity_id']] = $rel['position'];
        }
        //Zend_Debug::dump($relation);
		//Zend_Debug::dump($new);
        //Zend_Debug::dump($old);die;

        $insert = array_diff_assoc($new, $old);
        $delete = array_diff_assoc($old, $new);
		//Zend_Debug::dump($insert);
        //Zend_Debug::dump($delete);die;
        //Zend_Debug::dump(array_keys($delete));die;
    	if (!empty($delete)) {
            $where = join(' AND ', array(
                $this->_getWriteAdapter()->quoteInto('new_id=?', $parentId),
                $this->_getWriteAdapter()->quoteInto( 'entity_id IN(?)', array_keys($delete) )
            ));
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
        
        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $key=>$val) {
                $insertData[] = array(
                    'new_id' => $parentId,
                    'entity_id'  => $key,
                	'position'	 => $val,
                );
            }
            //Zend_Debug::dump($insertData);die;
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $insertData);
        }

        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('new_id = ?', $parentId);
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

        return parent::_afterSave($object);
    }
	
    
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('cmspro/relation'), 'new_id='.$object->getId());

	}
	
}