<?php
class MW_Cmspro_Block_View extends Mage_Core_Block_Template 
{
    public function _prepareLayout()
    {
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'cmspro';
        if($this->getRequest()->getParam('id')){
            //set title
            /*$curNews = $this->getNews();
            $headBlock = $this->getLayout()->getBlock('head');
            if($curNews && $headBlock){
            	var_dump($headBlock->setTitle($curNews->getPageTitle()));
            }*/
            
	        // show breadcrumbs
	        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
				//echo get_class($breadcrumbs); 
				//var_dump($breadcrumbs); die;
				$crumbs = array();
	            //$breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
				$crumbs[] = array('label'=>Mage::helper('cmspro')->__('Home'), 'title'=>Mage::helper('cmspro')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl());
				
		        // get array of selected store_id 
				$news =  Mage::getModel('cmspro/news')->getCollection();				
				$news->getSelect()
					->join(
						array('news_category'=>$news->getTable('news_category')), 
						'news_category.news_id = main_table.news_id AND main_table.news_id='.$this->getRequest()->getParam('id'), 
						array('news_category.category_id')
					)
					;
					//echo $news->getSelect();die;
				if($news->getData()){
					$data = $news->getData();
					if(isset($_SESSION['cmspro_current_cat'])){
						$category = Mage::getModel('cmspro/category')->load($_SESSION['cmspro_current_cat']);	
					}else{
						$category = Mage::getModel('cmspro/category')->load($data[0]['category_id']);
					}
					$root_path = $category->getRootPath();
					$root_path = explode('/',trim($root_path));

					foreach($root_path as $key=>$parent){
						if($parent!=""){
							$parent = Mage::getModel('cmspro/category')->load($parent);
							if($parent->getName()!=""){
								//$breadcrumbs->addCrumb('category_'.$key, array('label'=>$parent->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$parent->getName(), 'link'=>Mage::getBaseUrl().$parent->_getUrlRewrite()));
								$crumbs[] = array('label'=>$parent->getName(), 'title'=>Mage::helper('cmspro')->__('Return to ').$parent->getName(), 'link'=>Mage::getBaseUrl().$parent->_getUrlRewrite());
							}
						}
					}
					//$breadcrumbs->addCrumb('news_title', array('label'=>$data[0]['title'], 'title'=>$data[0]['title']));
					$crumbs[] = array('label'=>$data[0]['title'], 'title'=>$data[0]['title']);
				}
	            $_last = count($crumbs)-1;
	            unset($crumbs[$_last]['link']);
				$crumbs[$_last]['last'] = 1;				
				$this->getLayout()->getBlock('breadcrumbs')->setCrumbs($crumbs);
				$this->getLayout()->getBlock('breadcrumbs')->setTemplate('cmspro/html/breadcrumbs.phtml');
	        }
        }
        return parent::_prepareLayout();   
    }
    
    public function getNews()
    {    
    	if($this->getRequest()->getParam('id')){
	        $news = Mage::getModel('cmspro/news')->load($this->getRequest()->getParam('id'));
			
	        // add restriction filter
			$session = Mage::getSingleton('customer/session');
			$cid = ','.$session->getId().',';$cig=','.$session->getCustomerGroupId().',';
			$group=$news->getGroups();$user=$news->getUsers();
			$g=strpos('//'.$group,$cig);$u=strpos('//'.$user,$cid);
			if($group==',all,' and $user!=''){
				if($u===false) return null;
			};
			if($group!=',all,'&$user==''){
				if($g===false) return null;
			};
				
	        $version = Mage::getVersion();
        	if(version_compare($version, '1.4.0.1', '>=')===true){
		        // Generate html from code block if exist
		        $helper = Mage::helper('cms');
		        $processor = $helper->getPageTemplateProcessor();
		        $html = $processor->filter($news->getContent());
		        $html = $this->getMessagesBlock()->getGroupedHtml() . $html;
		        $news->setContent($html);
		        
		        $html = $processor->filter($news->getSummary());
		        $html = $this->getMessagesBlock()->getGroupedHtml() . $html;
		        $news->setSummary($html);
	        }
	        return $news;
    	}
    	return false;
    }

    public function _getNewerNews($next=false){
    	
    	if($this->getRequest()->getParam('id')){
    		$limit = 1;
    		if(!$next) {
    			$limit = (Mage::getStoreConfig('mw_cmspro/info/number_newer_news')) ? Mage::getStoreConfig('mw_cmspro/info/number_newer_news'):5;	
    		}
    		$news = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time','ASC')->setPageSize($limit);

    		// get category news
    		$category = Mage::getModel('cmspro/category')->getCollection()->addFieldToFilter('status','1');
			$category->getSelect()
				->join(
					array('category'=>$category->getTable('news_category')),
					'category.category_id = main_table.category_id AND category.news_id = '.$this->getRequest()->getParam('id'),
					array('category.news_id')
					)
				->join(
					array('store_cat'=>$category->getTable('category_store')),
					'store_cat.category_id = main_table.category_id',
					array('store_cat.store_id')
				)
				->where('store_cat.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
				;
			$categoryIds = array();
			foreach($category as $key=>$c){
				$categoryIds[] = $c->getCategoryId();
			}

			$news->getSelect()
				->join(
					array('news_category'=>$news->getTable('news_category')), 
					'news_category.news_id = main_table.news_id', 
					array('news_category.category_id')
				)
				->where('news_category.category_id in (?)',$categoryIds)
				->where('main_table.news_id not in (?) ',$this->getRequest()->getParams('id'))
				->where("main_table.created_time >='".$this->getNews()->getCreatedTime()."'")
				->group('main_table.news_id')
				;
				
			// add restriction filter
			$session = Mage::getSingleton('customer/session');
			$cid = $session->getId();$cig=$session->getCustomerGroupId();
			$news->addRestrictionFilter($cig,$cid);	
		
    		if($next){
    			if($news->count()>0){
    				$n = $news->getData();
    				return Mage::getModel('cmspro/news')->load($n[0]['news_id']);
    			}else return false;
    		}else{
    			return $news;
    		}
    	}
    	return false;	
    }
	
    public function _getOlderNews($previous=false){
    	if($this->getRequest()->getParam('id')){
    		$limit = 1;
    		if(!$previous){
    			$limit = (Mage::getStoreConfig('mw_cmspro/info/number_older_news')) ? Mage::getStoreConfig('mw_cmspro/info/number_older_news'):5;
    		}
    		$news = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time','DESC')->setPageSize($limit);
    		
    		// get category news
			$category = Mage::getModel('cmspro/category')->getCollection()->addFieldToFilter('status','1');
			$category->getSelect()
				->join(
					array('category'=>$category->getTable('news_category')),
					'category.category_id = main_table.category_id AND category.news_id = '.$this->getRequest()->getParam('id'),
					array('category.news_id')
					)
				->join(
					array('store_cat'=>$category->getTable('category_store')),
					'store_cat.category_id = main_table.category_id',
					array('store_cat.store_id')
				)
				->where('store_cat.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
				;
			$categoryIds = array();
			foreach($category as $key=>$c){
				$categoryIds[] = $c->getCategoryId();
			}

			$news->getSelect()
				->join(
					array('news_category'=>$news->getTable('news_category')), 
					'news_category.news_id = main_table.news_id', 
					array('news_category.category_id')
				)
				->where('news_category.category_id in (?)',$categoryIds)
				->where('main_table.news_id not in (?) ',$this->getRequest()->getParams('id'))
				->where("main_table.created_time <='".$this->getNews()->getCreatedTime()."'")
				->group('main_table.news_id')
				;		
				
			// add restriction filter
			$session = Mage::getSingleton('customer/session');
			$cid = $session->getId();$cig=$session->getCustomerGroupId();
			$news->addRestrictionFilter($cig,$cid);
			
    		if($previous){
    			if($news->count()>0){
    				$n = $news->getData();
    				return Mage::getModel('cmspro/news')->load($n[0]['news_id']);
    			}else return false;
    		}else{
    			return $news;
    		}
    	}
    	return false;	
    }
    
    public function _getCategory(){
    	if($this->getRequest()->getParam('id')){

			$news =  Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('category_id','ASC');
			$news->getSelect()
				->join(
					array('news_category'=>$news->getTable('news_category')), 
					'news_category.news_id = main_table.news_id 
						AND main_table.news_id='.$this->getRequest()->getParam('id'),
					 array('news_category.category_id')
				)
				->join(
					array('store_table'=>$news->getTable('category_store')),
					'store_table.category_id = news_category.category_id',
					array('store_table.store_id')
				)
				->where('store_table.store_id in (?)',array('0',Mage::app()->getStore()->getId()))
				->group('main_table.news_id')
				;
			
			// add restriction filter
			$session = Mage::getSingleton('customer/session');
			$cid = $session->getId();$cig=$session->getCustomerGroupId();
			$news->addRestrictionFilter($cig,$cid);
				
			if($news->getData()){
				$data = $news->getData();
				if(isset($_SESSION['cmspro_current_cat'])){
					return Mage::getModel('cmspro/category')->load($_SESSION['cmspro_current_cat']);	
				}else{
					return Mage::getModel('cmspro/category')->load($data[0]['category_id']);
				}
			}
			return false;
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
