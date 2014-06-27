<?php 

class MW_Cmspro_ViewController extends Mage_Core_Controller_Front_Action
{
   /*public function indexAction(){
		$this->_redirect('/');
    }*/
    
    public function detailsAction()
    {
    	
    	//var_dump($this->getRequest()->getParam('id')); exit;
    	$uri = explode('/cmspro/',$_SERVER['REQUEST_URI']);
		if(sizeof($uri)>1){
			//$this->_forward('defaultNoRoute');
		}else{
	    	$news = Mage::getModel('cmspro/news')->load($this->getRequest()->getParam('id'), 'news_id');
	    	//news is disabled
	    	if($news->getStatus() != 1){
	    		$this->_redirectError();
	    	}

			$this->loadLayout();
			
			$title = $news->getPageTitle();
			$meta_description = $news->getMetaDescription();
	    	$meta_keyword = $news->getMetaKeyword();
			if($title== '') $title = Mage::getStoreConfig('design/head/default_title');
			if($meta_description == '') $meta_description = Mage::getStoreConfig('design/head/default_description');
			if($meta_keyword == '') $meta_keyword = Mage::getStoreConfig('design/head/default_keywords');
			
	    	if ($this->getLayout()->getBlock('head')) {
	    		$head = $this->getLayout()->getBlock('head');
	            $head->setTitle($title);
	            $head->setDescription($meta_description);
	            $head->setKeywords($meta_keyword);
	        }
	        if(Mage::helper('cmspro/page')->isActiveLayout($this->getRequest()->getParam('id')) == MW_Cmspro_Model_Newsdesign::USE_OWN){
	        	Mage::helper('cmspro/page')->renderPage($this, $this->getRequest()->getParam('id'));
	        }
	        
	        $this->renderLayout(); 
		}
    }
}