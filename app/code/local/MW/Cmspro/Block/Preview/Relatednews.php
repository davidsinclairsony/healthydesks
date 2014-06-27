<?php
class MW_Cmspro_Block_Preview_Relatednews extends Mage_Catalog_Block_Product_Abstract
{
    protected $_itemCollection;

	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}
	
    protected function _prepareData()
    {
    	$newsId = $this->getRequest()->getParam('id');
    	$data = $this->getRequest()->getPost();
    	$links = $this->getRequest()->getPost('links');

	    $this->_itemCollection =  Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1);
	    
	    if(!isset($links['relatednews']) && $this->getRequest()->getParam('id')){
	    	
	    	$relationTableName = $this->_itemCollection->getTable('cmspro/news_news');
		    $this->_itemCollection->getSelect()
		    	 ->join(array('newsnews'=>$relationTableName), 'main_table.news_id = newsnews.related_news_id',array('related_news_id','position'))
				 ->where('newsnews.news_id='.$newsId );
				 
			$this->_itemCollection->getSelect()->order('newsnews.position');
	    	
	    }else{
	    	$array_related_news = array();
	    	$array_related_news = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['relatednews']));
	    	$this->_itemCollection->addFieldToFilter('news_id',array('in'=>$array_related_news));
	    }
 
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
