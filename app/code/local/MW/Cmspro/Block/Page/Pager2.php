<?php

class MW_Cmspro_Block_Page_Pager2 extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_pageVarName     = 'page';
    protected $_limitVarName    = 'limit';
    protected $_availableLimit  = array(5=>5);
    protected $_dispersion      = 3;
    protected $_displayPages    = 5;
    protected $_showPerPage		= true;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cmspro/list/pager.phtml');
    }   
    
   	public function getTag()
    {
        return Mage::registry('cmspro_current_tag');
    }    
    
    public function getCurrentPage()
    {
        if ($page = (int) $this->getRequest()->getParam($this->getPageVarName())) {
            return $page;
        }
        return 1;
    }

    public function getLimit()
    {
        $limits = $this->getAvailableLimit();
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits[$limit])) {
                return $limit;
            }
        }
        $limits = array_keys($limits);
        return Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value') ? Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value'):10;
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection
            ->setCurPage($this->getCurrentPage());
        // If not int - then not limit
        if ((int) $this->getLimit()) {
            $this->_collection->setPageSize($this->getLimit());
        }
        return $this;
    }

    /**
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getCollection()
    {
    	//Mage_Core_Model_Mysql4_Collection_Abstract
    	return $this->_collection;
    }

    public function setPageVarName($varName)
    {
        $this->_pageVarName = $varName;
        return $this;
    }

    public function getPageVarName()
    {
        return $this->_pageVarName;
    }

    public function setShowPerPage($varName)
    {
    	$this->_showPerPage=$varName;
    }

    public function getShowPerPage()
    {
        if(sizeof($this->getAvailableLimit())<=1) {
            return false;
        }
    	return $this->_showPerPage;
    }

    public function setLimitVarName($varName)
    {
        $this->_limitVarName = $varName;
        return $this;
    }

    public function getLimitVarName()
    {
        return $this->_limitVarName;
    }

    public function setAvailableLimit(array $limits)
    {
    	$this->_availableLimit = array();
    	$limits = Mage::getStoreConfig('mw_cmspro/info/news_per_page_allowed') ? Mage::getStoreConfig('mw_cmspro/info/news_per_page_allowed'):'5,10,15';
    	$limits = explode(',',$limits);
    	foreach($limits as $limit){
    		if(is_numeric($limit)) { $this->_availableLimit[$limit] = $limit;} 
    	}
       // $this->_availableLimit = $limits;
    }

    public function getAvailableLimit()
    {
    	$this->_availableLimit = array();
    	$limits = Mage::getStoreConfig('mw_cmspro/info/news_per_page_allowed') ? Mage::getStoreConfig('mw_cmspro/info/news_per_page_allowed'):'5,10,15';
    	$limits = explode(',',$limits);
    	foreach($limits as $limit){
    		$this->_availableLimit[$limit] = $limit; 
    	}
        return $this->_availableLimit;
    }

    public function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+1;
    }

    public function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize()*($collection->getCurPage()-1)+$collection->count();
    }

    public function getTotalNum()
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
					
	    /*$currentPage = (int)$this->getRequest()->getParam('page');
	    if(!$currentPage){
	        $currentPage = 1;
	    }
	    
	    $currentLimit = (int)$this->getRequest()->getParam('limit');
	    if(!$currentLimit){
	        $currentLimit = Mage::getStoreConfig('mw_cmspro/info/news_per_page_default_value');
	    }
	    
	    $collection->setPageSize($currentLimit);
	    $collection->setCurPage($currentPage);*/
	    //echo $collection->getSelect();die;
        if($collection){
        	return sizeof($collection->getData());
        }else{
        	return 0;
        }
    }

    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    public function isLastPage()
    {
        return $this->getCollection()->getCurPage() >= $this->getLastPageNum();
    }

    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    public function isPageCurrent($page)
    {
        return $page == $this->getCurrentPage();
    }

    public function getPages()
    {
        $collection = $this->getCollection();

        $pages = array();
        if ($collection->getLastPageNumber() <= $this->_displayPages) {
            $pages = range(1, $collection->getLastPageNumber());
        }
        else {
            $half = ceil($this->_displayPages / 2);
            if ($collection->getCurPage() >= $half && $collection->getCurPage() <= $collection->getLastPageNumber() - $half) {
                $start  = ($collection->getCurPage() - $half) + 1;
                $finish = ($start + $this->_displayPages) - 1;
            }
            elseif ($collection->getCurPage() < $half) {
                $start  = 1;
                $finish = $this->_displayPages;
            }
            elseif ($collection->getCurPage() > ($collection->getLastPageNumber() - $half)) {
                $finish = $collection->getLastPageNumber();
                $start  = $finish - $this->_displayPages + 1;
            }

            $pages = range($start, $finish);
        }

        return $pages;

//        $pages = array();
//        for ($i=$this->getCollection()->getCurPage(-$this->_dispersion); $i <= $this->getCollection()->getCurPage(+($this->_dispersion-1)); $i++)
//        {
//
//            $pages[] = $i;
//        }
//
//        return $pages;
    }

    public function getFirstPageUrl()
    {
        return $this->getPageUrl(1);
    }

    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(-1));
    }

    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(+1));
    }

    public function getLastPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getLastPageNumber());
    }

    public function getPageUrl($page)
    {
        return $this->getPagerUrl(array($this->getPageVarName()=>$page));
    }

    public function getLimitUrl($limit)
    {
        return $this->getPagerUrl(array($this->getLimitVarName()=>$limit));
    }

    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
}