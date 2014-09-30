<?php 
class MW_Cmspro_Block_Relatednews extends Mage_Catalog_Block_Product_Abstract 
{
    protected $_itemCollection;

	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}   
	public function getnewscollection(){
		$collection=  Mage::getModel('cmspro/relation')
				->getCollection()->addFieldToFilter('entity_id',array('eq'=>$this->getProduct()->getId()));	
				if($collection->count() <=0) return null;
				$news_entity=array();
       foreach($collection as $news){
			$news_entity[]=$news->getNewId();			
	   };
	   $news =  Mage::getModel('cmspro/news')->getCollection();				
		$news->addFieldToFilter('news_id', array('in' =>$news_entity))
		 ->setOrder('created_time','desc')
		->setPageSize(Mage::getStoreConfig('mw_cmspro/product/number_related_news') ? Mage::getStoreConfig('mw_cmspro/product/number_related_news'):5);
		;
		
	    // add restriction filter
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$news->addRestrictionFilter($cig,$cid);		
		
		return $news;
	} 

    public function getItems()
    {
        return $this->_itemCollection;
    }
}