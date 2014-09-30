<?php
class MW_Cmspro_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('cmspro/tag');
	}
	
    /**
     * Adds filter by status
     *
     * @param int $status
     * @return MW_Cmspro_Model_Mysql4_Tag_Collection
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    /**
     * Adds filter by news id
     *
     * @param int $productId
     * @return MW_Cmspro_Model_Mysql4_Tag_Collection
     */
    public function addNewsFilter($newsId)
    {
    	 $this->getSelect()
            ->join(
                array('relation' => $this->getTable('cmspro/news_tag')),
                'main_table.tag_id = relation.tag_id',
                array()
            );
        $this->addFieldToFilter('relation.news_id', $newsId);
       /* if ($this->getFlag('prelation')) {
            $this->addFieldToFilter('prelation.product_id', $newsId);
        }*/
        return $this;
    }	
}