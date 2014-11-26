<?php
class MW_Cmspro_Block_Feature extends Mage_Core_Block_Template 
{	
    public function getLatestNews(){
		
		//$this->cmsprosession();
		$configSize = Mage::getStoreConfig('mw_cmspro/news/number_news_latest') ? Mage::getStoreConfig('mw_cmspro/news/number_news_latest'):10;
		$size = $this->latestSize ? $this->latestSize:$configSize;
    	$news = Mage::getModel('cmspro/news')->getCollection()
     		->addStatusFilter(1)
    		->setOrder('created_time','desc')
    		->setPageSize($size);
	    if (!Mage::app()->isSingleStoreMode()) {
			$news->getSelect()
				->join(
					array('category'=>$news->getTable('news_category')),
					'main_table.news_id = category.news_id',
					array('category.category_id')
				)
				->join(
					array('store_table' => $news->getTable('category_store')),
					'category.category_id = store_table.category_id',
					array('store_table.store_id')
				)
				->where('store_table.store_id in (?)', array(0, Mage::app()->getStore()->getId()))
				->group('main_table.news_id');
	    }
	    
	    // add restriction filter
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$news->addRestrictionFilter($cig,$cid);
    	return $news;
    }
    
    public function getFeaturesNews(){
		$size = $this->featureSize ? $this->featureSize:4;
    	$collection = Mage::getModel('cmspro/news')->getCollection()
    		->addStatusFilter(1)
    		->addFieldToFilter('feature','1')
    		->setOrder('created_time','desc')
    		->setPageSize($size);

	    //add storeview filter
	    $storeView = Mage::app()->getStore()->getId();
	    $collection->getSelect()->join(array('ns'=>$collection->getTable('news_store')),
	    							   'main_table.news_id = ns.news_id') ;
	    $collection->addFieldToFilter('ns.store_id',array('fdin'=>array($storeView,0)));			
				
	    // add restriction filter
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$collection->addRestrictionFilter($cig,$cid);
				
    	return $collection->getData();
    }

    public function getNewsThumbnailSize(){
    	$size = Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size') ? Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size'):"175-131";
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
		return array('width'=>175,'height'=>131);
    }
    public function getLatestNewsThumbnailSize(){
    	$size = Mage::getStoreConfig('mw_cmspro/info/latest_news_thumbnail_size') ? Mage::getStoreConfig('mw_cmspro/info/latest_news_thumbnail_size'):"50-50";
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:50,'height'=>is_numeric($tmp[1])?$tmp[1]:50);
		return array('width'=>50,'height'=>50);
    }
	
}