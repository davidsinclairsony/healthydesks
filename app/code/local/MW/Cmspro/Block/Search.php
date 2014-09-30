<?php
class MW_Cmspro_Block_Search extends Mage_Core_Block_Template
{
	protected $_defaultToolbarBlock = 'cmspro/page_pager1';
    public function _prepareLayout()
    {
        //$route = Mage::helper('news')->getRoute();
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'cmspro';
        
        // show breadcrumbs
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
                $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));;
                $news = Mage::getModel('cmspro/category')->load(1);
                $breadcrumbs->addCrumb('news', array('label'=>$news->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$news->getName(), 'link'=>Mage::getBaseUrl().$news->_getUrlRewrite()));
                //$breadcrumbs->addCrumb('cmspro', array('label'=>'News', 'title'=>Mage::helper('cmspro')->__('Return to news'), 'link'=>Mage::getUrl('cmspro')));
        }
                        
        return parent::_prepareLayout();   
    }
    
    public function getFormActionUrl()
    {
		return $this->getUrl('cmspro/search/result', array('_secure' => true));
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

	    if ($this->getRequest()->get('keyword')) {
		    
        	$keyword = $this->getRequest()->get('keyword');
        	$keyword = preg_replace('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/','', $keyword);

			$collection->getSelect()
				->where(
					"main_table.title LIKE '%".$keyword."%' OR 
					 main_table.summary LIKE '%".$keyword."%' OR 
					 main_table.content LIKE '%".$keyword."%'
					");
	    }	
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
   
    public function closetags($html){
        return Mage::helper('cmspro/data')->closetags($html);
    }

     public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }
    
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getNews();
        
        // set collection to tollbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
       /* Mage::dispatchEvent('cmspro_block_cmspro_news_collection', array(
            'collection'=>$collection,
        ));*/

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