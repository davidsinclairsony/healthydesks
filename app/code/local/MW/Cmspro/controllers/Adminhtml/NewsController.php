<?php

class MW_Cmspro_Adminhtml_NewsController extends Mage_Adminhtml_Controller_action
{
	protected $_tagOptionsArray = array();
	
	protected $_collection;
	
	protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }
	
	 /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
		
    }
	
	/**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }
	
	protected function _initNews()
    {
	 $newsId  = (int) $this->getRequest()->getParam('id');
	 
        $news    = Mage::getModel('cmspro/news')
            ->setStoreId($this->getRequest()->getParam('store', 0));
		
		
		$news->setData('_edit_mode', true);
		
		if ($newsId) {
            try {
                $news->load($newsId);
            } catch (Exception $e) {
               
                Mage::logException($e);
            }
        }
		$attributes = $this->getRequest()->getParam('attributes');
       
	}
	
	/**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName)
        ;
    }
	
	public function relatedAction()
    {
        $this->_initNews();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.news.related')
        	->setProductsRelated($this->getRequest()->getPost('products_related', null))
		   ;
		   
        $this->renderLayout();
		
    }
	
	/**
     * Get related products grid
     */
    public function relatedGridAction()
    {
        $this->_initNews();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.news.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    //1.6.5:related news
	public function relatedNewsAction()
    {
        //$this->_initNews();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.news.relatednews')
        	->setProductsRelated($this->getRequest()->getPost('related_news', null))
		   ;
		   
        $this->renderLayout();
    }
    
	/**
     * Get related news grid
     */
    public function relatedNewsGridAction()
    {
        //$this->_initNews();
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.news.relatednews')
            ->setProductsRelated($this->getRequest()->getPost('related_news', null));
        $this->renderLayout();
    }
    //1.6.7: news tag
    /**
     * Get tag grid
     */
    public function tagGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('cmspro.admin.product.tags')
            ->setNewsId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }
    
	protected function _initAction() 
	{
		$this->loadLayout()
			->_setActiveMenu('cmspro/news')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('cmspro/news');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
			
            if (!$model->getNewsId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmspro')->__('This page no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cmspronews_data', $model);

        // 5. Build edit form
        $this->loadLayout()
			->_setActiveMenu('cmspro/news')
            ->_addBreadcrumb($id ? Mage::helper('cmspro')->__('Edit News') : Mage::helper('cmspro')->__('Add News'), $id ? Mage::helper('cmspro')->__('Edit News') : Mage::helper('cmspro')->__('Add News'));

        $this->renderLayout();
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		$redirectBack   = $this->getRequest()->getParam('back', false);
		$data = $this->getRequest()->getPost();
		$newsId = $this->getRequest()->getParam('id');
		//zend_debug::dump($data); exit;
		//$tagNamesArray = $this->_cleanTags($data['tags']);
		//var_dump($tagNamesArray);die;
		if ($data) {
			// Upload images new:
			if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('images');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS .'news' ;
					//$path = Mage::getBaseDir('media') . DS .'news'.DS ;
					$file_name = $uploader->getCorrectFileName($_FILES['images']['name']);	
					//$uploader->save($path, $_FILES['images']['name'] );
					$uploader->save($path, $file_name);
				} catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;	
				} catch (Exception $e) {
				   Mage::logException($e);
					$this->_getSession()->addError($e->getMessage());
					$redirectBack = true;
		        }	        	        
		        //this way the name is saved in DB
	  			//$data['images'] = 'news/'.$_FILES['images']['name'];
	  			$data['images'] = 'news/'.$file_name;
			}else{

				if(isset($data['images']['delete']) && $data['images']['delete']==1){ 
					$data['images']=""; 
				}if(isset($data['images']['value']) && $data['images']['value'] != ''){
					$data['images'] = $data['images']['value'];
				}else{
					unset($data['images']);
				}
			}
			
			if ($redirectBack) {
				$this->_redirect('*/*/edit', array(
					'id'    => $newsId,
					'_current'=>true
				));
			}

	  		// Rewrite URL
			$suffix = Mage::getStoreConfig('mw_cmspro/info/category_suffix') ? Mage::getStoreConfig('mw_cmspro/info/category_suffix'):""; 
			if($data['identifier']!=""){
				$url_key = $data['identifier'];
			}else{
				$url_key = $data['title'];
				$data['identifier'] = $data['title'];
			}
			
			$root = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format(Mage::getModel('cmspro/category')->getCurrentRoot()->getName()));
			$root = strtolower($root);
			$url_key = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($url_key));
			$url_key = strtolower($url_key);
			$rq_path = $root."/".$url_key;
			$rq_path1 = trim($rq_path, '-').$suffix;
			
			//save users and groups infomation.
			$users=explode(",",$this->getRequest()->getPost('users'));			
			$collection= Mage::getSingleton('customer/customer')->getCollection();
			$collection->addFieldToFilter('email',array('in' => $users));$str=",";
			foreach($collection as $us){
				$str=$str.$us->getEntityId().",";
			};
			if($str!=',')$data['users']=$str;
			
			$groups=$this->getRequest()->getPost('guser');$str=",";
			foreach($groups as $group){
				if($group == 'all'){
					$str .= 'all,'; break;
				}
				$str=$str.$group.",";
			};
			if($str!=','){
				$data['groups']=$str;
			}
			else $data['groups']= ',all,';
			//Mage::log($data);
			
			// Luu vao CSDL
	  		$model = Mage::getModel('cmspro/news');
			//$data['status']=$data['active'];
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				
				if (!$newsId) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				
				// Save data
				$model->save();
				
				$this->updateNewsBackup($model->getId(),$data);
				
				//set related product
				$links = $this->getRequest()->getPost('links');
				if (isset( $links['related']) ){
					Mage::getModel('cmspro/relation')->setRelatedProducts( $model->getId(), Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));				
				}
				//1.6.5 set related news
				if (isset($links['relatednews']) ) {
		        	Mage::getModel('cmspro/newsnews')->setRelatedNews( $model->getId(),Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['relatednews']));
		        }
        		
		        // update table news_category
				$newsResourceModel = Mage::getResourceModel('cmspro/news');
				$newsResourceModel->saveNewsCategory($model);
		        
				// Update URL:
				$current_news = Mage::getModel('cmspro/news')->load($model->getId());
				$url_rewrite = array();
		     	$url_rewrite['request_path'] = $rq_path1;
		     	$url_rewrite['target_path'] = 'cmspro/view/details/id/'.$model->getId();
		     	$url_rewrite['id_path'] = $rq_path1;
		     	$url_rewrite['is_system'] = 1;
		     	$url_rewrite['options'] = 0;
				
		     	$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
		     	// if($url_model1->count()>0){
		     		// $rq_path2 = $rq_path.rand(100,99999).$suffix;
		     		// $url_rewrite['request_path'] = $rq_path2;
		     		// $url_rewrite['id_path'] = $rq_path2;
		     	// }
				$url_model = Mage::getModel('core/url_rewrite');
		     	if($current_news->getUrlRewriteId()=="0") { 
					$url_model->setData($url_rewrite);
					$url_model->save();
					$model->setUrlRewriteId($url_model->getId());
					$model->save();
				}else{
					$url_model->setData($url_rewrite)->setId($current_news->getUrlRewriteId());
					$url_model->save();
				}
				
				//update table news_store
				$storeIds = array();
				$conn = Mage::getModel('core/resource')->getConnection('core_write');
				$selectQuery =  "SELECT DISTINCT cs.store_id FROM ".$newsResourceModel->getTable('cmspro/category_store')." AS cs 
				INNER JOIN ".$newsResourceModel->getTable('cmspro/news_category')." AS ns ON cs.category_id = ns.category_id
				WHERE ns.news_id ='".$model->getId()."'";
		   		$deleteQuery = "DELETE FROM ".$newsResourceModel->getTable('cmspro/news_store')." WHERE news_id ='".$model->getId()."'";
				$storeIds = $conn->fetchAll($selectQuery);
		   		$conn->query($deleteQuery);
		   		sort($storeIds);
		   		//var_dump($storeIds);die;
		   		foreach ($storeIds as $storeId) {
			   		$insertQuery = "INSERT INTO ".$newsResourceModel->getTable('cmspro/news_store');
			   		$insertQuery .= " VALUES (".$model->getId().",".$storeId["store_id"]." )";
			   		$conn->query($insertQuery);
			   		if($storeId["store_id"] == 0) break;
		   		}
		   		
				//save tag
		   		if(isset($data['tags'])){
					$tagNamesArray = $data['tags'];
					//$tagNamesArray = $this->explode(",",$data['tags']);
					$tagIdsArray =  array();
			        $tagModel = Mage::getModel('cmspro/tag');
			        $newsTagModel = Mage::getModel('cmspro/newstag');
			        foreach ($tagNamesArray as $tagName)
			        {
			        	$tagName = trim($tagName);
			        	//var_dump($tagName);die;
			        	if($tagName != '')
			        	{
				        	$tagModel->unsetData()->loadByName($tagName);
				        	//if tag already exist...
				        	if($tagModel->getId())
				        	{
				        		$tagIdsArray[] = $tagModel->getId();
				        	}else{//tag is NOT already exist: create tag
				        		$tagModel->unsetData();
				        		$tagModel->setName($tagName);
				        		$tagModel->setStatus(1);
				        		$tagModel->setPopularity(0);
				        		$tagModel->save();
				        		$tagIdsArray[] = $tagModel->getId();
				        	}
			        	}
			        }
			        $newsTagModel->assignTagsToNews($model, $tagIdsArray);
		   		}
				
				//savedesign
				$this->saveDesign($model);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cmspro')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmspro')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	
	public function updateNewsBackup($news_id_parent,$data){
		$resource = Mage::getSingleton('core/resource');
		$model = Mage::getModel('cmspro/newsbackup');
		
		$data['images'] = Mage::getModel('cmspro/news')->load($news_id_parent)->getImages(); 
		$data['created_time'] = now();
		$data['update_time'] = now();
		$data['news_id_parent'] = $news_id_parent;
		$model->setData($data)->save();
		$new_id = $model->getId();
		
		Mage::getResourceModel('cmspro/newsbackup')->saveNewsCategory($new_id,$data);
		
		//update table news_store
		$storeIds = array();
		$conn = Mage::getModel('core/resource')->getConnection('core_write');
		
		$selectQuery =  "SELECT DISTINCT cs.store_id FROM ".$resource->getTableName('cmspro/category_store')." AS cs 
		INNER JOIN ".$resource->getTableName('cmspro/news_category_backup')." AS ns ON cs.category_id = ns.category_id
		WHERE ns.news_id ='".$new_id."'";
		
   		$deleteQuery = "DELETE FROM ".$resource->getTableName('cmspro/news_store_backup')." WHERE news_id ='".$new_id."'";
   		
		$storeIds = $conn->fetchAll($selectQuery);
   		$conn->query($deleteQuery);
   		sort($storeIds);
   		
   		foreach ($storeIds as $storeId) {
   			
	   		$insertQuery = "INSERT INTO ".$resource->getTableName('cmspro/news_store_backup');
	   		$insertQuery .= " VALUES (".$new_id.",".$storeId["store_id"]." )";
	   		$conn->query($insertQuery);
	   		if($storeId["store_id"] == 0) break;
   		}
	}
	public function duplicateAction() {
		$data = $this->getRequest()->getPost();
		$newsId = $this->getRequest()->getParam('id');

		if ($data) {
			$images = '';
			$images = $this->saveDuplicate($data,$newsId,1,null);
			$this->saveDuplicate($data,null,2,$images);
        }else{
        	 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmspro')->__('Unable to find item to save'));
        	$this->_redirect('*/*/');
        }
        
       
	}
	public function saveDuplicate($data,$newsId,$i,$images)
	{
			if($i == 1){
				if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('images');
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir
						//$path = Mage::getBaseDir('media') . DS .'news'.DS ;
						$path = Mage::getBaseDir('media') . DS .'news' ;
						$file_name = $uploader->getCorrectFileName($_FILES['images']['name']);	
						//$uploader->save($path, $_FILES['images']['name'] );
						$uploader->save($path,$file_name);
						
					} catch (Mage_Core_Exception $e) {
	               		 $this->_getSession()->addError($e->getMessage())
	                    	->setProductData($data);	
					} catch (Exception $e) {
					    Mage::logException($e);
						$this->_getSession()->addError($e->getMessage());
			        }	        	        
			        //this way the name is saved in DB
		  			//$data['images'] = 'news/'.$_FILES['images']['name'];
		  			$data['images'] = 'news/'.$file_name;
				}else{
					if(isset($data['images']['delete']) && $data['images']['delete']==1){ 
						$data['images']=""; 
					}if(isset($data['images']['value']) && $data['images']['value'] != ''){
						$data['images'] = $data['images']['value'];
					}else{
						unset($data['images']);
					}
				}
			}else{
				
				$data['images'] = $images;

			}
			
	  		// Rewrite URL
			$suffix = Mage::getStoreConfig('mw_cmspro/info/category_suffix') ? Mage::getStoreConfig('mw_cmspro/info/category_suffix'):""; 
			if($data['identifier']!=""){
				$url_key = $data['identifier'];
			}else{
				$url_key = $data['title'];
				$data['identifier'] = $data['title'];
			}
			
			$root = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format(Mage::getModel('cmspro/category')->getCurrentRoot()->getName()));
			$root = strtolower($root);
			$url_key = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($url_key));
			if($i == 2) {
				$url_key = $url_key.'-new';
				$data['identifier'] = $data['identifier'].' new';
				
			}
			$url_key = strtolower($url_key);
			$rq_path = $root."/".$url_key;
			$rq_path1 = trim($rq_path, '-').$suffix;
			
			//save users and groups infomation.
			$users = explode(",",$this->getRequest()->getPost('users'));			
			$collection= Mage::getSingleton('customer/customer')->getCollection();
			$collection->addFieldToFilter('email',array('in' => $users));$str=",";
			foreach($collection as $us){
				$str=$str.$us->getEntityId().",";
			};
			if($str!=',')$data['users']=$str;
			
			$groups = $this->getRequest()->getPost('guser');$str=",";
			foreach($groups as $group){
				if($group == 'all'){
					$str .= 'all,'; break;
				}
				$str=$str.$group.",";
			};
			if($str!=','){
				$data['groups']=$str;
			}
			else $data['groups']= ',all,';
			
	  		$model = Mage::getModel('cmspro/news');
			//$data['status']=$data['active'];
			$model->setData($data)
				->setId($newsId);
			
			try {
				
				if (!$newsId) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				
				// Save data
				$model->save();
				
				$this->updateNewsBackup($model->getId(),$data);
				
				//set related product
				$links = $this->getRequest()->getPost('links');
				if (isset( $links['related']) ){
					Mage::getModel('cmspro/relation')->setRelatedProducts( $model->getId(), Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));				
				}
				//1.6.5 set related news
				if (isset($links['relatednews']) ) {
		        	Mage::getModel('cmspro/newsnews')->setRelatedNews( $model->getId(),Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['relatednews']));
		        }
        		
		        // update table news_category
				$newsResourceModel = Mage::getResourceModel('cmspro/news');
				$newsResourceModel->saveNewsCategory($model);
		        
				// Update URL:
				$current_news = Mage::getModel('cmspro/news')->load($model->getId());
				$url_rewrite = array();
		     	$url_rewrite['request_path'] = $rq_path1;
		     	$url_rewrite['target_path'] = 'cmspro/view/details/id/'.$model->getId();
		     	$url_rewrite['id_path'] = $rq_path1;
		     	$url_rewrite['is_system'] = 1;
		     	$url_rewrite['options'] = 0;
				
		     	$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
				$url_model = Mage::getModel('core/url_rewrite');
		     	if($current_news->getUrlRewriteId()=="0") { 
					$url_model->setData($url_rewrite);
					$url_model->save();
					$model->setUrlRewriteId($url_model->getId());
					$model->save();
				}else{
					$url_model->setData($url_rewrite)->setId($current_news->getUrlRewriteId());
					$url_model->save();
				}
				
				//update table news_store
				$storeIds = array();
				$conn = Mage::getModel('core/resource')->getConnection('core_write');
				$selectQuery =  "SELECT DISTINCT cs.store_id FROM ".$newsResourceModel->getTable('cmspro/category_store')." AS cs 
				INNER JOIN ".$newsResourceModel->getTable('cmspro/news_category')." AS ns ON cs.category_id = ns.category_id
				WHERE ns.news_id ='".$model->getId()."'";
		   		$deleteQuery = "DELETE FROM ".$newsResourceModel->getTable('cmspro/news_store')." WHERE news_id ='".$model->getId()."'";
				$storeIds = $conn->fetchAll($selectQuery);
		   		$conn->query($deleteQuery);
		   		sort($storeIds);
		   		//var_dump($storeIds);die;
		   		foreach ($storeIds as $storeId) {
			   		$insertQuery = "INSERT INTO ".$newsResourceModel->getTable('cmspro/news_store');
			   		$insertQuery .= " VALUES (".$model->getId().",".$storeId["store_id"]." )";
			   		$conn->query($insertQuery);
			   		if($storeId["store_id"] == 0) break;
		   		}
		   		
				//save tag
				if(isset($data['tags'])){
					$tagNamesArray = $data['tags'];
					$tagIdsArray =  array();
			        $tagModel = Mage::getModel('cmspro/tag');
			        $newsTagModel = Mage::getModel('cmspro/newstag');
			        foreach ($tagNamesArray as $tagName)
			        {
			        	$tagName = trim($tagName);
			        	if($tagName != '')
			        	{
				        	$tagModel->unsetData()->loadByName($tagName);
				        	//if tag already exist...
				        	if($tagModel->getId())
				        	{
				        		$tagIdsArray[] = $tagModel->getId();
				        	}else{
				        		$tagModel->unsetData();
				        		$tagModel->setName($tagName);
				        		$tagModel->setStatus(1);
				        		$tagModel->setPopularity(0);
				        		$tagModel->save();
				        		$tagIdsArray[] = $tagModel->getId();
				        	}
			        	}
			        }
			        $newsTagModel->assignTagsToNews($model, $tagIdsArray);
				}
				
				$this->saveDesign($model);
				
				
				if($i == 2)
				{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cmspro')->__('The news has been duplicated'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
					
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
            		return;
				}
            
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
            if($i == 1 && $model->getId()){ 

            	return $model->load($model->getId())->getImages();
            }
            
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('cmspro/news');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function reindexAction(){
    	try{
    		$news = Mage::getModel('cmspro/news');
    		$news->reindexNews();
    		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('All News were reindexed Successful'));
    	}catch(Exception $e){
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occured, please try again'));
    	}
    	$this->_redirect('*/*/index'); 
    }	
	
    public function massDeleteAction() {
        $categoryIds = $this->getRequest()->getParam('news');
        if(!is_array($categoryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    $category = Mage::getModel('cmspro/news')->load($categoryId);
                    $category->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($categoryIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $categoryIds = $this->getRequest()->getParam('news');
        if(!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    $category = Mage::getSingleton('cmspro/news')
                        ->load($categoryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($categoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'cmspro.csv';
        $content    = $this->getLayout()->createBlock('cmspro/adminhtml_news_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'cmspro.xml';
        $content    = $this->getLayout()->createBlock('cmspro/adminhtml_news_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
 	/**
     * Checks inputed tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    protected function _extractTags($tagNamesInString)
    {
        return preg_replace("/(\'(.*?)\')|(\s+)/i", "$1 ", $tagNamesInString);
    }    
    
	/**
     * Clears the tag from the separating characters.
     *
     * @param array $tagNamesArr
     * @return array
     */
    protected function _cleanTags(array $tagNamesArr)
    {
        foreach( $tagNamesArr as $key => $tagName ) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if( $tagNamesArr[$key] == '' ) {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    } 

    /**
     * Save action
     */
    public function saveDesign($news)
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            //Zend_Debug::dump($this->getRequest()->getPost());
            //Zend_Debug::dump($data);die;
            //init model and set data
            $model = Mage::getModel('cmspro/newsdesign');

            /*Zend_Debug::dump($id);
			Zend_Debug::dump($model->getId());die;*/
            
            $model->setData($data)
            	  ->setId($news->getId()) ;

            //Mage::dispatchEvent('cms_page_prepare_save', array('page' => $model, 'request' => $this->getRequest()));

            //validating
            if (!Mage::helper('cmspro')->getProEdition() AND !$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // clear previously saved data from session
                //Mage::getSingleton('adminhtml/session')->setFormData(false);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('cmspro')->__('Error not saved yet.'));
            }
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
        return $data;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            /** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            if (!empty($data['custom_layout_update_xml'])
            && !$validatorCustomLayout->isValid($data['custom_layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }

	public function autoCompleteAction()
    {
    	$tagOptionsArray = $this->tagToOptionsArray();
    	$jsonTag = json_encode($tagOptionsArray);
    	echo $jsonTag;
    }
    
	public function tagToOptionsArray()
  	{
  		$collection = $this->_getCollection();
  		if( $collection AND (!$this->_tagOptionsArray) )
  		{
  			foreach ($collection as $tag)
  			{
  				$option['value'] = $tag->getName();
  				$option['key'] = $tag->getName();
  				$this->_tagOptionsArray[] = $option;
  			}
  		} 
  		return $this->_tagOptionsArray;
  	}
  	    
  	protected function _getCollection()
  	{
  		if(!$this->_collection)
  		{
	        $model = Mage::getModel('cmspro/tag');
	        $this->_collection = $model->getResourceCollection()
						        //->addStatusFilter(MW_Cmspro_Model_Tag::STATUS_ENABLED)
						        ->load();
  		}
  		return $this->_collection;
  	}
}