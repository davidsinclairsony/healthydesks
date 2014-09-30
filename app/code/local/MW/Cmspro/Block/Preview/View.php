<?php
class MW_Cmspro_Block_Preview_View extends Mage_Core_Block_Template 
{
 	public function _prepareLayout()
    {
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'cmspro';
        $data = $this->getRequest()->getPost();
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
			$crumbs = array();
            
			$crumbs[] = array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl());
			if($data['news_category'][0]){
				if(isset($_SESSION['cmspro_current_cat'])){
					$category = Mage::getModel('cmspro/category')->load($_SESSION['cmspro_current_cat']);	
				}else{
					$category = Mage::getModel('cmspro/category')->load($data['news_category'][0]);
				}
				$root_path = $category->getRootPath();
				$root_path = explode('/',trim($root_path));

				foreach($root_path as $key=>$parent){
					if($parent!=""){
						$parent = Mage::getModel('cmspro/category')->load($parent);
						if($parent->getName()!=""){
							$crumbs[] = array('label'=>$parent->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$parent->getName(), 'link'=>Mage::getBaseUrl().$parent->_getUrlRewrite());
						}
					}
				}
				$crumbs[] = array('label'=>$data['title'], 'title'=>$data['title']);
			}
            $_last = count($crumbs)-1;
            unset($crumbs[$_last]['link']);
			$crumbs[$_last]['last'] = 1;				
			$this->getLayout()->getBlock('breadcrumbs')->setCrumbs($crumbs);
			$this->getLayout()->getBlock('breadcrumbs')->setTemplate('cmspro/html/breadcrumbs.phtml');
	    }
        return parent::_prepareLayout();   
    }
    public function getNews()
    {   
    	//Zend_Debug::dump($this->getRequest()->getPost());
    	$path = Mage::getBaseDir('media') . DS .'mw_news_preview'.DS;
		foreach(glob($path ."*.*") as $file) {
		    @unlink($file);
		}

    	$data = $this->getRequest()->getPost();
    	$row = array();
    	if ($data) {
			// Upload images new:
			if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
				
				$uploader = new Varien_File_Uploader('images');
				
	           	$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(false);
				
				$uploader->setFilesDispersion(false);
						
				$path = Mage::getBaseDir('media') . DS .'mw_news_preview'.DS ;
				$uploader->save($path, $_FILES['images']['name'] );
				        	        
	  			$data['images'] = 'mw_news_preview/'.$_FILES['images']['name'];
			} 
			if(isset($data['images']['value']) && $data['images']['value'] != '') $data['images'] = $data['images']['value'];
			if(!isset($data['images'])) $data['images'] = '';
	    	$row['images'] = $data['images'];
	    	$row['title'] = $data['title'];
	    	$row['summary'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($data['summary']);
	    	
	    	$row['created_time'] = now();
	    	$row['allow_show_image'] = $data['allow_show_image'];
	    	$row['author'] = $data['author'];
	    	$row['content'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($data['content']);
	    	
	       	$rowObj = new Varien_Object();
		    $rowObj->setData($row); 
		    return $rowObj;
		}
		
	    return false;
    }

    public function _getNewerNews($next=false)
    {
    	$limit = 1;
    	$data = $this->getRequest()->getPost();
    	if(!$next) {
    		$limit = (Mage::getStoreConfig('mw_cmspro/info/number_newer_news')) ? Mage::getStoreConfig('mw_cmspro/info/number_newer_news'):5;	
    	} 
    	$news = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time','ASC')->setPageSize($limit);

    	// get category news
    	$category = Mage::getModel('cmspro/category')->getCollection()->addFieldToFilter('status','1');
		$category->getSelect()
			->join(
				array('store_cat'=>$category->getTable('category_store')),
				'store_cat.category_id = main_table.category_id',
				array('store_cat.store_id')
			)
			->where('main_table.category_id in (?)',$data['news_category'])
			->where('store_cat.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
			;
		$categoryIds = array();
		foreach($category as $key=>$c){
			$categoryIds[] = $c->getCategoryId();
		}

		$create_time = now();
		
		if($this->getRequest()->getParam('id'))
			$create_time = Mage::getModel('cmspro/news')->load($this->getRequest()->getParam('id'))->getCreatedTime();
			
		$news->getSelect()
			->join(
				array('news_category'=>$news->getTable('news_category')), 
				'news_category.news_id = main_table.news_id', 
				array('news_category.category_id')
			)
			->where('news_category.category_id in (?)',$categoryIds)
			->where("main_table.created_time >='".$create_time."'")
			->group('main_table.news_id')
			;
    	if($next){
    		if($news->count()>0){
    			$n = $news->getData();
    			return Mage::getModel('cmspro/news')->load($n[0]['news_id']);
    		}else return false;
    	}else{
    		return $news;
    	}
    	return false;	
    	
    }
	
    public function _getOlderNews($previous=false)
    {
    	$limit = 1;
    	$data = $this->getRequest()->getPost();
    	
    	if(!$previous){
    		$limit = (Mage::getStoreConfig('mw_cmspro/info/number_older_news')) ? Mage::getStoreConfig('mw_cmspro/info/number_older_news'):5;
    	}
    	$news = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time','DESC')->setPageSize($limit);
    	
		$category = Mage::getModel('cmspro/category')->getCollection()->addFieldToFilter('status','1');
		$category->getSelect()

			->join(
				array('store_cat'=>$category->getTable('category_store')),
				'store_cat.category_id = main_table.category_id',
				array('store_cat.store_id')
			)
			->where('main_table.category_id in (?)',$data['news_category'])
			->where('store_cat.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
			;
		$categoryIds = array();
		foreach($category as $key=>$c){
			$categoryIds[] = $c->getCategoryId();
		}
		if($this->getRequest()->getParam('id'))
			$create_time = Mage::getModel('cmspro/news')->load($this->getRequest()->getParam('id'))->getCreatedTime();
			
		$news->getSelect()
			->join(
				array('news_category'=>$news->getTable('news_category')), 
				'news_category.news_id = main_table.news_id', 
				array('news_category.category_id')
			)
			->where('news_category.category_id in (?)',$categoryIds)
			->where("main_table.created_time <='".$create_time."'")
			->group('main_table.news_id')
			;		
		
    	if($previous){
    		if($news->count()>0){
    			$n = $news->getData();
    			return Mage::getModel('cmspro/news')->load($n[0]['news_id']);
    		}else return false;
    	}else{
    		return $news;
    	}
    	return false;	
    	
    }
    
    public function _getCategory()
    {
		$data = $this->getRequest()->getPost();
		$news =  Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('category_id','ASC');
		$news->getSelect()
			->join(
					array('news_category'=>$news->getTable('news_category')), 
					'news_category.news_id = main_table.news_id',
					 array('news_category.category_id')
				)
			->join(
				array('store_table'=>$news->getTable('category_store')),
				'store_table.category_id = news_category.category_id',
				array('store_table.store_id')
			)
			->where('news_category.category_id in (?)',$data['news_category'])
			->where('store_table.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
			->group('main_table.news_id')
			;
		if($news->getData()){
			$data = $news->getData();
			return Mage::getModel('cmspro/category')->load($data[0]['category_id']);
	
		}

    	return false;
    }
    
    public function getNewsThumbnailSize(){
    	$size = Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size') ? Mage::getStoreConfig('mw_cmspro/info/news_thumbnail_size'):"175-131";
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
		return array('width'=>175,'height'=>131);
    }
}
