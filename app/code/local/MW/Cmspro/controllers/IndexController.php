<?php

class MW_Cmspro_IndexController extends Mage_Core_Controller_Front_Action
{
	public function backupAction() {
		if(!$this->getRequest()->isXmlHttpRequest()) {
			Zend_Debug::dump('hack');die;
		}
		
		$data = $this->getRequest()->getPost();
		$new_id = 0;
		if(isset($data['mw_news_backup'])) $new_id = $data['mw_news_backup'];
		if($new_id != 0){
			$arrCategoryId = array();
				  	
         	$collection_categories =  Mage::getModel('cmspro/newsbackup')->getCollection();
			$table_cat = $collection_categories->getTable('news_category_backup'); 

			$collection_categories->getSelect()
						->join(array('table_cat'=>$table_cat), 'table_cat.news_id = main_table.news_id', array('table_cat.category_id'))
						->where('main_table.news_id=?',$new_id);
						

			if($collection_categories->getData()){
				foreach($collection_categories->getData() as $col){
					if(isset($col['category_id'])) { $arrCategoryId[] = $col['category_id'];}
				}
			}
			
			$model = Mage::getModel('cmspro/newsbackup')->load($new_id);
			
			$groups = array('all');
			$users = "";
			if($model->getNewsId()){  						
				$arrgroup = explode(",",$model->getGroups());
				$i=0;
				foreach($arrgroup as $groupid){													
					if($groupid == null||$groupid == ""||$groupid==" ") 
					{
						unset($arrgroup[$i]);						
					};
						$i++;
				}	
				$arruser = explode(",",$model->getUsers());
				$i=0;		 
				foreach($arruser as $userid){													
					if($userid == null||$userid == ""||$userid==" ") 
					{
					unset($arrgroup[$i]);					
					};
						$i++;
				}
				$collection_users = Mage::getSingleton('customer/customer')->getCollection();
				$collection_users->addFieldToFilter('entity_id',array('in' => $arruser));
				$str = "";
				foreach ($collection_users as $collection_user){
					$str = $str.$collection_user->getEmail().",";
				};
 
				$groups = array();
				foreach ($arrgroup as $arrgroup_new) {
					$groups[] = $arrgroup_new;
				}
				$users = $str;
			 }
			 
			 
    		$images = $model->getImages();
    		$html_images = '';
    		if($images != ''){
    			 $url_media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
    			 $html_images ='<td class="label"><label for="images">Images</label></td><td class="value"><a onclick="imagePreview("images_image"); return false;" href="'.$url_media.$images.'"><img width="22" height="22" class="small-image-preview v-middle" alt="'.$images.'" title="'.$images.'" id="images_image" src="'.$url_media.$images.'"></a> <input type="file" class="input-file" value="'.$images.'" name="images" id="images"><span class="delete-image"><input type="checkbox" id="images_delete" class="checkbox" value="1" name="images[delete]"><label for="images_delete"> Delete Image</label><input type="hidden" value="'.$images.'" name="images[value]"></span></td>';
    		}else{
    			$html_images = '<td class="label"><label for="images">Images</label></td><td class="value"><input type="file" class="input-file" value="" name="images" id="images"></td>';
    		}
    		
    		
    		header('content-type: text/javascript');
    		
			$jsondata = array("title"=>$model->getTitle(), 
							  "identifier"=>$model->getIdentifier(),
			                  "category_id"=>$arrCategoryId,
			                  "summary"=>$model->getSummary(),
			                  "content"=>$model->getContent(), 
			                  "status"=>$model->getStatus(),
			                  "page_title"=>$model->getPageTitle(),
			                  "meta_keyword"=>$model->getMetaKeyword(),
			                  "meta_description"=>$model->getMetaDescription(), 
			                  "feature"=>$model->getFeature(),
							  "author"=>$model->getAuthor(),
			                  "allowcomment"=>$model->getAllowcomment(),
			                  "allow_show_image"=>$model->getAllowShowImage(),
			                  "groups"=>$groups, 
			                  "users"=>$users,
			                  "restrict"=>$model->getRestrict(),
							  "images"=>$html_images);
			
			echo json_encode($jsondata);
			die();	
    	}
	}
    public function indexAction()
    {
    	$uri = explode('/cmspro',$_SERVER['REQUEST_URI']);
		if(sizeof($uri)>1){
			$this->_forward('defaultNoRoute');
		}else{
    		$this->loadLayout();

	    	if ($this->getLayout()->getBlock('head')) {
	    		$head = $this->getLayout()->getBlock('head');
	            $head->setTitle(Mage::helper('cmspro')->getTitleIndex());
	            $head->setDescription(Mage::helper('cmspro')->getMetaDescriptionIndex());
	            $head->setKeywords(Mage::helper('cmspro')->getMetaKeywordIndex());
	        }
    		
			$this->renderLayout();
		}
    }
    public function previewAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();
    	
    }
    public function testAction()
    {
    	$newsIdsArray = array();
    	$catResource = Mage::getResourceModel('cmspro/category');
		$catIdsArray = $catResource->getChildrenIds(2);
		foreach($catIdsArray as $id){
			$newsCollection = Mage::getModel('cmspro/news')->getCollection();
			$newsCollection->addFieldToSelect('news_id');
			$newsCollection->getSelect()->join(array('nc'=>$catResource->getTable('cmspro/news_category')),
											  'main_table.news_id = nc.news_id',array(''))
										->where('nc.category_id = '.$id);
			$newsIdsArray = array_merge($newsIdsArray,$newsCollection->getAllIds());
		}
		Zend_Debug::dump(array_unique($newsIdsArray));
    }
    
/*	public function autoComplete()
    {
    	return '[{"key": "hello world", "value": "hello world"}, {"key": "movies", "value": "movies"}, {"key": "ski", "value": "ski"}, {"key": "snowbord", "value": "snowbord"}, {"key": "computer", "value": "computer"}, {"key": "apple", "value": "apple"}, {"key": "pc", "value": "pc"}, {"key": "ipod", "value": "ipod"}, {"key": "ipad", "value": "ipad"}, {"key": "iphone", "value": "iphone"}, {"key": "iphon4", "value": "iphone4"}, {"key": "iphone5", "value": "iphone5"}, {"key": "samsung", "value": "samsung"}, {"key": "blackberry", "value": "blackberry"}]';
    }*/
}