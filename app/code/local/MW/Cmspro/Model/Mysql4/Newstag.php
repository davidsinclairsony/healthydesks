<?php
class MW_Cmspro_Model_Mysql4_Newstag extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('cmspro/news_tag', 'relation_id');
		//var_dump($this->getTable('cmspro/tag'));die;
	}
	
	/**
     * Add TAG to NEWS relations
     *
     * @param MW_Cmspro_Model_Newstag $model
     * @return MW_Cmspro_Model_Resource_Newstag
     */
	public function assignNewsToTag($model)
    {
        $addedIds = $model->getAddedNewsIds();

        $bind = array(
            'tag_id'   => $model->getTagId(),
        );
        
        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($this->getMainTable(), 'news_id')
            ->where('tag_id = :tag_id');
            
        $oldRelationIds = $write->fetchCol($select, $bind);
		
        $insert = array_diff($addedIds, $oldRelationIds);
        $delete = array_diff($oldRelationIds, $addedIds);

        /*Zend_Debug::dump($bind);
        Zend_Debug::dump($addedIds);
        Zend_Debug::dump($oldRelationIds);
		Zend_Debug::dump($insert);
		Zend_Debug::dump($delete);die;*/
		
        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $value) {
                $insertData[] = array(
                    'tag_id'        => $model->getTagId(),
                    'news_id'    	=> $value,
                    'created_at'    => $this->formatDate(time())
                );
            }
            $write->insertMultiple($this->getMainTable(), $insertData);
        }

        if (!empty($delete)) {
            $write->delete($this->getMainTable(), array(
                'news_id IN (?)' => $delete,
            	'tag_id IN (?)' => $model->getTagId(),
            ));
        }

        return $this;
    }
    
	/**
     * Add TAG(S) to NEWS relations
     *
     * @param MW_Cmspro_Model_Newstag $model
     * @return MW_Cmspro_Model_Resource_Newstag
     */
	public function assignTagsToNews($model) 
    {
        $addedIds = $model->getAddedTagsIds();
		
        $bind = array(
            'news_id'   => $model->getNewsId(),
        );
        
        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($this->getMainTable(), 'tag_id')
            ->where('news_id = :news_id');
            
        $oldRelationIds = $write->fetchCol($select, $bind);
		
        $insert = array_diff($addedIds, $oldRelationIds);
        $delete = array_diff($oldRelationIds, $addedIds);

        /*Zend_Debug::dump($bind);
        Zend_Debug::dump($addedIds);
        Zend_Debug::dump($oldRelationIds);
		Zend_Debug::dump($insert);
		Zend_Debug::dump($delete);die;*/
		
        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $value) {
                $insertData[] = array(
                    'tag_id'        => $value,
                    'news_id'    	=> $model->getNewsId(),
                    'created_at'    => $this->formatDate(time())
                );
            }
            $write->insertMultiple($this->getMainTable(), $insertData);
            //increase popularity
            Mage::getResourceModel('cmspro/tag')->incrementPopularity(array($insert));
        }

        if (!empty($delete)) {
            $write->delete($this->getMainTable(), array(
                'tag_id IN (?)' => $delete,
            	'news_id IN (?)' => $model->getNewsId(),
            ));
            //decrease popularity
            Mage::getResourceModel('cmspro/tag')->decrementPopularity(array($delete));
        }

        return $this;
    }    
    
    /**
     * Retrieve Tagged News
     *
     * @param MW_Cmspro_Model_NewsTag $model
     * @return array
     */
    public function getNewsIds($model)
    {
        $bind = array(
            'tag_id' => $model->getTagId()
        );
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'news_id')
            ->where($this->getMainTable() . '.tag_id=:tag_id');

       /* if (!is_null($model->getCustomerId())) {
            $select->where($this->getMainTable() . '.customer_id= :customer_id');
            $bind['customer_id'] = $model->getCustomerId();
        }

        if ($model->hasStoreId()) {
            $select->where($this->getMainTable() . '.store_id = :store_id');
            $bind['store_id'] = $model->getStoreId();
        }*/

        if (!is_null($model->getStatusFilter())) {
            $select->join(
                $this->getTable('cmspro/tag'),
                $this->getTable('cmspro/tag') . '.tag_id = ' . $this->getMainTable() . '.tag_id'
            )
            ->where($this->getTable('cmspro/tag') . '.status = :t_status');
            $bind['t_status'] = $model->getStatusFilter();
        }

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }
    
    /**
     * Load by Tag and Customer
     *
     * @param MW_Cmspro_Model_Newstag $model
     * @return MW_Cmspro_Model_Mysql4_Newstag
     */
    public function loadByNewsTag($model)
    {
        if ($model->getTagId() && $model->getNewsId()) {
            $read = $this->_getReadAdapter();
            $bind = array(
                'tag_id'      => $model->getTagId(),
                'news_id' => $model->getNewsId()
            );

            $select = $read->select()
                ->from($this->getMainTable())
                ->join(
                    array('t' => $this->getTable('cmspro/tag')),
                    't.tag_id = ' . $this->getMainTable() . '.tag_id'
                )
                ->where($this->getMainTable() . '.tag_id = :tag_id')
                ->where('news_id = :news_id');

            if ($model->getNewsId()) {
                $select->where($this->getMainTable() . '.news_id = :news_id');
                $bind['news_id'] = $model->getNewsId();
            }
            $data = $read->fetchRow($select, $bind);
            $model->setData(( is_array($data) ) ? $data : array());
        }

        return $this;
    }    
}