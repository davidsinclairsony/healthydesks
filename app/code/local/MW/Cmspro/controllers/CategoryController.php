<?php

class MW_Cmspro_CategoryController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->_redirect('/');
    }
    
    public function viewAction(){
    	$uri = explode('/cmspro/',$_SERVER['REQUEST_URI']);
		if(sizeof($uri)>1){
			$this->_forward('defaultNoRoute');
		}else{
	    	$this->loadLayout();
	    	
	    	$category = Mage::getModel('cmspro/category')->load($this->getRequest()->get('id'), 'category_id');
	    	$title = $category->getPageTitle();
	    	$meta_description = $category->getMetaDescription();
	    	$meta_keyword = $category->getMetaKeyword();
			if($title == '') $title = Mage::getStoreConfig('design/head/default_title');
			if($meta_description == '') $meta_description = Mage::getStoreConfig('design/head/default_description');
			if($meta_keyword == '') $meta_keyword = Mage::getStoreConfig('design/head/default_keywords');
				    	
	    	if ($this->getLayout()->getBlock('head')) {
	    		$head = $this->getLayout()->getBlock('head');
	            $head->setTitle($title);
	            $head->setDescription($meta_description);
	            $head->setKeywords($meta_keyword);
	        }
	        $this->renderLayout();
		}
    }
}