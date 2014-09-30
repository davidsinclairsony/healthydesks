<?php
class MW_Cmspro_Block_Tag_List extends Mage_Core_Block_Template
{
	
	protected $_defaultToolbarBlock = 'cmspro/page_pager2';
	
   	public function getTag()
    {
        return Mage::registry('cmspro_current_tag');
    }
    
    public function getNews()     
    {
	    // Get news order by created_time by store view
	    $collection = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time', 'desc');		
	    
		//add storeview filter
	    $storeView = Mage::app()->getStore()->getId();
	    $collection->getSelect()->join(array('ns'=>$collection->getTable('news_store')),
	    							   'main_table.news_id = ns.news_id') ;
	    $collection->addFieldToFilter('ns.store_id',array('fdin'=>array($storeView,0)));
		//$collection->getSelect()->group('main_table.news_id');

	    //tag search filter
	    $tagId = $this->getTag()->getId();
		$collection->getSelect()
			->join(array('nt'=>$collection->getTable('news_tag')),'main_table.news_id = nt.news_id')
			->where(
				"nt.tag_id = '".$tagId."'");
		
		// add restriction filter	
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$collection->addRestrictionFilter($cig,$cid);			
					
	    $currentPage = (int)$this->getRequest()->getParam('page');
	    if(!$currentPage){
	        $currentPage = 1;
	    }
	    
	    $currentLimit = (int)$this->getRequest()->getParam('limit');
	    if(!$currentLimit){
	        $currentLimit = Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value');
	    }
	    
	    $collection->setPageSize($currentLimit);
	    $collection->setCurPage($currentPage);
	    return $collection;
    }

    public function getHeaderText()
    {
        if( $this->getTag()->getName() ) {
            return Mage::helper('cmspro')->__("Products tagged with '%s'", $this->htmlEscape($this->getTag()->getName()));
        } else {
            return false;
        }
    }    
    
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getNews();
        
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        
        $this->getNews()->load();
        return parent::_beforeToHtml();
    }    
    
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve list toolbar HTMLs
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }    
    
    public function getNewsThumbnailSize(){
    	$size = Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size') ? Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size'):"175-131";
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
		return array('width'=>175,'height'=>131);
    }    
}