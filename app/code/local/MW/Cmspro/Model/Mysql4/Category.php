<?php

class MW_Cmspro_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the category_id refers to the key field in your database table.
        $this->_init('cmspro/category', 'category_id');
    }
    
    public function saveCategoryStores(Mage_Core_Model_Abstract $object)
    {
    	$conn = Mage::getModel('core/resource')->getConnection('core_write');
		$currentId = $object->getData('category_id');
		$childrenIds = $this->getChildrenIds($currentId);
		$categoryTableName = $this->getTable('cmspro/category');
		$categoryStoreTableName = $this->getTable('cmspro/category_store');
		
		//update store view for editing category and all of it's descendant
		foreach ($childrenIds as $id) {
			$parentId = $conn->fetchOne("SELECT parent_id FROM ".$categoryTableName." WHERE category_id = '".$id."'");
			$deleteQuery = 'DELETE FROM '.$categoryStoreTableName.' WHERE category_id = '.$id;
			$conn->query($deleteQuery);
	        //allow choose store view only if category is child of news category(news category is root category, scope = all store view).
			if($parentId == 1)
			{
				if (!$object->getData('category_stores'))
				{
					$insertQuery = 'INSERT INTO '.$categoryStoreTableName;
		            $insertQuery .= ' VALUES ('.$id.',0)';
		            $conn->query($insertQuery);
				}
				else{
					foreach ((array)$object->getData('category_stores') as $store) 
					{
						$insertQuery = 'INSERT INTO '.$categoryStoreTableName;
						$insertQuery .= ' VALUES ('.$id.','.$store.')';
						$conn->query($insertQuery);
					}
				}
			}
			
			//Otherwise category will inherit store view from it's parent category
			else 
			{
				//$storeArr = array();
				$readresult=$conn->query("SELECT store_id FROM ".$categoryStoreTableName." WHERE category_id='".$parentId."'");
				while ($row = $readresult->fetch() )
				{
					$insertQuery = 'INSERT INTO '.$categoryStoreTableName;
					$insertQuery .= ' VALUES ('.$id.','.$row['store_id'].')';
					$conn->query($insertQuery);
				}
			}
		}
    }
	
    public function getChildrenIds($catId)
    {
    	$conn = Mage::getModel('core/resource')->getConnection('core_write');
    	$categoryTableName = $this->getTable('cmspro/category');
		//get rootpath of editing category
		$rootPath = $conn->fetchOne("SELECT root_path FROM ".$categoryTableName." WHERE category_id = '".$catId."'");
		
		//get all descendant by rootpath
		if($rootPath){
			$readresult=$conn->query("SELECT category_id FROM ".$categoryTableName." WHERE root_path LIKE '%".$rootPath."%'");
	   		 while ($row = $readresult->fetch() )
			{
				$childrenIds[] = $row['category_id'];
			}
		}
		return $childrenIds;
    } 
    
    protected function _beforeDelete123(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		//$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		//$adapter->delete($this->getTable('cmspro/category_store'), 'category_id='.$object->getId());

	}
}