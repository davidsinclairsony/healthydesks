<?php
class MW_Cmspro_Block_View_Relatednews extends Mage_Catalog_Block_Product_Abstract
{
    protected $_itemCollection;

	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}
	
    protected function _prepareData()
    {
    	$newsId = $this->getRequest()->getParam('id');
    	/*$collection=  Mage::getModel('cmspro/newsnews')
				->getCollection()->addFieldToFilter('news_id',array('eq'=>$newsId));
		$collection->getSelect()->order('position');
		if($collection->count() <=0) return null;
		$news_entity=array();
        foreach($collection as $news){
			$news_entity[]=$news->getRelatedNewsId();			
	    };*/
	    //Zend_Debug::dump($news_entity);die;
	    $this->_itemCollection =  Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1);
	    $relationTableName = $this->_itemCollection->getTable('cmspro/news_news');
	    $this->_itemCollection->getSelect()
	    	->join(array('newsnews'=>$relationTableName), 'main_table.news_id = newsnews.related_news_id',array('related_news_id','position'))
			->where('newsnews.news_id='.$newsId );
		$this->_itemCollection->getSelect()->order('newsnews.position');
		$this->_itemCollection->setPageSize(Mage::getStoreConfig('mw_cmspro/relatednews/number_related_news') ? Mage::getStoreConfig('mw_cmspro/relatednews/number_related_news'):5);
		
		// add restriction filter	
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$this->_itemCollection->addRestrictionFilter($cig,$cid);
		
		$this->_itemCollection->load();
		//echo $this->_itemCollection->getSelect();die;
		return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
}
