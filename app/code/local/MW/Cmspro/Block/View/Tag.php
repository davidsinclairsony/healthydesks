<?php
class MW_Cmspro_Block_View_Tag extends Mage_Core_Block_Template
{
	protected $_collection;
	
    public function getNewsId()
    {
        return ($this->getRequest()->getParam('id')) ? $this->getRequest()->getParam('id') : false;
    }

    public function getCount()
    {
        return count($this->getTags());
    }    
    
    public function getTags()
    {
        return $this->_getCollection()->getItems();
    }    
    
	protected function _getCollection()
    {
        if( !$this->_collection && $this->getNewsId() ) {

            $model = Mage::getModel('cmspro/tag');
            $this->_collection = $model->getResourceCollection()
                ->addStatusFilter(MW_Cmspro_Model_Tag::STATUS_ENABLED)
                ->addNewsFilter($this->getNewsId())
                ->load();
        }
        return $this->_collection;
    }

    /**
     * Render tags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @return string
     */
    public function renderTags($pattern, $glue = ' ')
    {
        $out = array();
        foreach ($this->getTags() as $tag) {
            $out[] = sprintf($pattern,
                $tag->getTaggedNewsUrl(), $this->htmlEscape($tag->getName()), $tag->getPopularity()
            );
        }
        return implode($out, $glue);
    }    

    /**
     * Generate unique html id
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHtmlId($prefix = '')
    {
        if (is_null($this->_uniqueHtmlId)) {
            $this->_uniqueHtmlId = Mage::helper('core/data')->uniqHash($prefix);
        }
        return $this->_uniqueHtmlId;
    }    
    
}