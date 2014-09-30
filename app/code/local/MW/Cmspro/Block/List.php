<?php
class MW_Cmspro_Block_List extends Mage_Core_Block_Template
{
	protected $_defaultToolbarBlock = 'cmspro/page_pager';
    
    public function _prepareLayout()
    {
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'cmspro';
        if($this->getRequest()->get('id')){ $id=$this->getRequest()->get('id');}else{$id=1;}
            
        // show breadcrumbs
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
        	$crumbs = array();
        	//$breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $crumbs[] = array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl());
			
			$category = Mage::getModel('cmspro/category')->load($id);
			$root_path = $category->getRootPath();
			
			$root_path = explode('/',trim($root_path,"/"));

			foreach($root_path as $key=>$parent){
				if($parent!=""){
					$parent = Mage::getModel('cmspro/category')->load($parent);
					if($parent->getName()!=""){
						//$breadcrumbs->addCrumb('category_'.$key, array('label'=>$parent->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$parent->getName(), 'link'=>Mage::getBaseUrl().$parent->_getUrlRewrite()));
						$crumbs[] = array('label'=>$parent->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$parent->getName(), 'link'=>Mage::getBaseUrl().$parent->_getUrlRewrite());
					}
				}	
			}
            $_last = count($crumbs)-1;
            unset($crumbs[$_last]['link']);
			$crumbs[$_last]['last'] = 1;			
			$this->getLayout()->getBlock('breadcrumbs')->setCrumbs($crumbs);
			$this->getLayout()->getBlock('breadcrumbs')->setTemplate('cmspro/html/breadcrumbs.phtml');
        }
    }

  
    public function getNews()     
    {
    	$_SESSION['cmspro_current_cat'] = 1;
	    // Get news order by created_time by store view
	    $collection = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time', 'desc');
	    
	    //add storeview filter
	    $storeView = Mage::app()->getStore()->getId();
	    $collection->getSelect()->join(array('ns'=>$collection->getTable('news_store')),
	    							   'main_table.news_id = ns.news_id') ;
	    $collection->addFieldToFilter('ns.store_id',array('fdin'=>array($storeView,0)));
	    
		// add restriction filter
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();$cig=$session->getCustomerGroupId();
		$collection->addRestrictionFilter($cig,$cid);
		
	    if ($this->getRequest()->get('id')) {
        	$category_id = $this->getRequest()->get('id');
			$_SESSION['cmspro_current_cat'] = $category_id;

        	$cat = Mage::getModel('cmspro/category')->load($category_id);
        	$table_category = $collection->getTable('category');

		    $collection->getSelect()
					->join(
						array('category'=>$collection->getTable('news_category')),
						"category.news_id = main_table.news_id 
							AND 
							 category.category_id in (
							 	SELECT ".$table_category.".category_id FROM ".$table_category." 
							 		WHERE ".$table_category.".root_path LIKE '".$cat->getRootPath()."%'
									AND ".$table_category.".status = 1
							 )
						",array('category.category_id'))
//				/*		->join(
//							array('store_table' => $collection->getTable('category_store')),
//							'category.category_id = store_table.category_id',
//							array('store_table.store_id')
//						)
//						->where('store_table.store_id in (?)', array('0', Mage::app()->getStore()->getId()*/))
						->group('main_table.news_id')
						;
	    }
		
	    $currentPage = (int)$this->getRequest()->getParam('page');
	    if(!$currentPage){
	        $currentPage = 1;
	    }
	    
	    $currentLimit = (int)$this->getRequest()->getParam('limit');
	    if(!$currentLimit){
	        $currentLimit = Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value') ? Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value'):5;
	    }	    
	    $collection->setPageSize($currentLimit);
	    $collection->setCurPage($currentPage);
	    //echo $collection->getSelect();die;
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
    
    public function getCategoryTitle() {
	    if ($this->getRequest()->get('id')) {
        	$category_id = $this->getRequest()->get('id');
        } else {
            return '';
            //$category_id = 1;
        }
        $cat = Mage::getModel('cmspro/category')->load($category_id);
        return $cat->getName();
    }    
    
}