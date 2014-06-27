<?php
class MW_Cmspro_Model_Newstag extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('cmspro/newstag');
	}
	
	/**
     * Add TAG to NEWS relations
     *
     * @param MW_Cmspro_Model_Tag $model
     * @param array $newsIds
     * @return MW_Cmspro_Model_Newstag
     */
	public function assignNewsToTag($model, $newsIds)
	{
		$this->setAddedNewsIds($newsIds);
        $this->setTagId($model->getTagId());
        $this->_getResource()->assignNewsToTag($this);
        return $this;
	}
	
	/**
     * Add TAG to NEWS relations
     *
     * @param MW_Cmspro_Model_News $model
     * @param array $tagIds
     * @return MW_Cmspro_Model_Newstag
     */
	public function assignTagsToNews($model, $tagIds)
	{
		$this->setAddedTagsIds($tagIds);
        $this->setNewsId($model->getId());
        $this->_getResource()->assignTagsToNews($this);
        return $this;
	}
	
    /**
     * Retrieve Relation News Ids
     *
     * @return array
     */
    public function getNewsIds()
    {
        $ids = $this->getData('news_ids');
        if (is_null($ids)) {
            $ids = $this->_getResource()->getNewsIds($this);
            $this->setNewsIds($ids);
        }
        return $ids;
    }
    
    /**
     * Load relation by news , tag
     *
     * @param int $newsId
     * @param int $tagId
     * @return MW_Cmspro_Model_NewsTag
     */
    public function loadRelationByNewsTag($newsId, $tagId)
    {
        $this->setNewsId($newsId);
        $this->setTagId($tagId);
        $this->_getResource()->loadByNewsTag($this);
        return $this;
    }    
}