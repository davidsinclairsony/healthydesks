<?php
class MW_Cmspro_Model_Mysql4_Rating_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('core_read'));
        $this->_ratingTable     = Mage::getSingleton('core/resource')->getTableName('rating/news_rating');
        $this->_ratingOptionTable   = Mage::getSingleton('core/resource')->getTableName('cmspro/news_rating_option');

        $this->_select->from($this->_ratingOptionTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('cmspro/news_rating_option'));
    }

    /**
     * add rating filter
     *
     * @param   int|array $rating
     * @return  Varien_Data_Collection_Db
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        }
        elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', array('in'=>$rating)), 'string');
        }
        return $this;
    }

    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Varien_Data_Collection_Db
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder($this->_ratingOptionTable.'.position', $dir);
        return $this;
    }
}
