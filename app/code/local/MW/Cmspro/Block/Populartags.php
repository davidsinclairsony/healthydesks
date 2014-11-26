<?php
class MW_Cmspro_Block_Populartags extends Mage_Core_Block_Template
{

    protected $_tags;
    protected $_minPopularity;
    protected $_maxPopularity;

    protected function _loadTags()
    {
        if (empty($this->_tags)) {
            $this->_tags = array();

            $tags = Mage::getModel('cmspro/tag')->getResourceCollection();
            
            $tags->getSelect()->limit(20);
            $tags->getSelect()->order('popularity ' . Varien_Db_Select::SQL_DESC);
            $tags = $tags->load()->getItems();
            
            if( count($tags) == 0 ) {
                return $this;
            }

            $this->_maxPopularity = reset($tags)->getPopularity();
            $this->_minPopularity = end($tags)->getPopularity();
            //var_dump($this->_maxPopularity);
            //var_dump($this->_minPopularity);die;
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ($range == 0) ? 1 : $range;
            foreach ($tags as $tag) {
                $tag->setRatio(($tag->getPopularity()-$this->_minPopularity)/$range);
                $this->_tags[$tag->getName()] = $tag;
            }
            ksort($this->_tags);
        }
        return $this;
    }

    public function getTags()
    {
        $this->_loadTags();
        return $this->_tags;
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }

    protected function _toHtml()
    {
        if (count($this->getTags()) > 0) {
            return parent::_toHtml();
        }
        return '';
    }
}