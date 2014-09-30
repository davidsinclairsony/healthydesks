<?php
class MW_Cmspro_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('cmspro/tag', 'tag_id');
		//var_dump($this->getTable('cmspro/tag'));die;
	}

    /**
     * Saving tag's popularity
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $tagId = ($object->isObjectNew()) ? $object->getTagId() : $object->getId();
        $writeAdapter = $this->_getWriteAdapter();
        $polularity = $writeAdapter->fetchOne('SELECT COUNT(news_id) FROM '.$this->getTable('cmspro/news_tag').' WHERE tag_id = '.$tagId);
        //var_dump($polularity);die;
        $writeAdapter->update($this->getTable('cmspro/tag'), array(
            'popularity'   => $polularity
        ),'tag_id = '.$tagId);

        return parent::_afterSave($object);
    }	
    
    /**
     * Decrementing tags popularity as action for news delete
     *
     * @param array $tagsId
     * @return int The number of affected rows
     */
    public function decrementPopularity(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($tagsId)) {
            return 0;
        }
        return $writeAdapter->update(
            $this->getTable('cmspro/tag'),
            array('popularity' => new Zend_Db_Expr('popularity - 1')),
            array('tag_id IN (?)' => $tagsId)
        );
    }    
    
    /**
     * Incrementing tags popularity when added in news edit from
     *
     * @param array $tagsId
     * @return int The number of affected rows
     */
    public function incrementPopularity(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($tagsId)) {
            return 0;
        }
        return $writeAdapter->update(
            $this->getTable('cmspro/tag'),
            array('popularity' => new Zend_Db_Expr('popularity + 1')),
            array('tag_id IN (?)' => $tagsId)
        );
    }
    
    /**
     * Loading tag by name
     *
     * @param MW_Cmspro_Model_Tag $model
     * @param string $name
     * @return array|false
     */
    public function loadByName($model, $name)
    {
        if ( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = :name');
            $data = $read->fetchRow($select, array('name' => $name));

            $model->setData(( is_array($data) ) ? $data : array());
        } else {
            return false;
        }
    }    
}