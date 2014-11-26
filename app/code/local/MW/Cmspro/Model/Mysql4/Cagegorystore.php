<?php

class MW_Cmspro_Model_Mysql4_Categorystore extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/categorystore', 'category_id');
    }
	
	
	public function getMainTable(){
		return $this->getTable('cmspro/categorystore')	;
	}
	
	
	public function processRelations($parentId, $childIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('entity_id'))
            ->where('new_id=?', $parentId);
        $old = $this->_getReadAdapter()->fetchCol($select);
        $new = $childIds;

        $insert = array_diff($new, $old);
        $delete = array_diff($old, $new);

        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $childId) {
                $insertData[] = array(
                    'new_id' => $parentId,
                    'entity_id'  => $childId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $insertData);
        }
        if (!empty($delete)) {
            $where = join(' AND ', array(
                $this->_getWriteAdapter()->quoteInto('new_id=?', $parentId),
                $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $delete)
            ));
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
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