<?php
class MW_Cmspro_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('category/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsActiveTabId();
        $this->_forward('edit');
    }
    
	public function editAction() {
		
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('cmspro/category')->setData(array());
		
		if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmspro')->__('This page no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        //$this->_title($id ? $model->getTitle() : $this->__('Add Category'));
		
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		
		Mage::register('cmsprocategory_data', $model);

		$this->loadLayout();
		$this->_setActiveMenu('category/items');

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('cmspro')->__('Edit Category') : Mage::helper('cmspro')->__('Add Category'), $id ? Mage::helper('cmspro')->__('Edit Category') : Mage::helper('cmspro')->__('Add Category'));

		$this->_addContent($this->getLayout()->createBlock('cmspro/adminhtml_category_edit'))
			->_addLeft($this->getLayout()->createBlock('cmspro/adminhtml_category_edit_tabs'));

		$this->renderLayout();
	}
 
	public function newAction() {
		$this->_forward('edit');
	}

    
	public function saveAction() {
		if ($this->getRequest()->getPost()) {
			$data = $this->getRequest()->getPost(); 
			if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('images');					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					/* Set the file upload mode false -> get the file directly in the specified folder true -> get the file in the product like folders ; (file.jpg will go in something like /media/f/i/file.jpg)*/
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					//$path = Mage::getBaseDir('media') . DS.'categories'.DS ;
					$path = Mage::getBaseDir('media') . DS.'categories' ;
					$file_name = $uploader->getCorrectFileName($_FILES['images']['name']);	
					//$uploader->save($path, $_FILES['images']['name'] );
					$uploader->save($path, $file_name );
					
				} catch (Exception $e) {
		      
		        }
		        //this way the name is saved in DB
	  			//$data['images'] = 'categories/'.$_FILES['images']['name'];
	  			$data['images'] = 'categories/'.$file_name;
				
			}else{
			
				if(isset($data['images']['delete']) && $data['images']['delete']==1){ 
					$data['images']=""; 
				}else{
					unset($data['images']);
				}
			}
			//save users and groups infomation.
			$users=explode(",",$this->getRequest()->getPost('users'));			
			 $collection= Mage::getSingleton('customer/customer')->getCollection();
			 $collection->addFieldToFilter('email',array('in' => $users));$str="";
			 foreach($collection as $us){
				$str=$str.$us->getEntityId().",";
			 };
			 $data['users']=$str;
			$groups = $this->getRequest()->getPost('guser');
			$str="";
			if(sizeof($groups) > 0){
				foreach($groups as $group){
					$str = $str.$group.",";
				};
			}
			
			$data['groups']= $str;//Mage::log($data);
			
			// Luu vao CSDL
	  		$model = Mage::getModel('cmspro/category');
			$model->setData($data)->setId($this->getRequest()->getParam('id'));
			
			//get parent_id from JSON_object and set for model
			$parentId = json_decode($this->getRequest()->getParam('parent_id'),true);
			$model->setParentId($parentId['parent_id']);
			
			try {
				if (!$this->getRequest()->getParam('id')) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				
				// Save data
				//Zend_Debug::dump($model->getData());die;			
				$model->save();
				
				// Update level and root_path and status for current category and its child
				$collections = Mage::getModel('cmspro/category')->getCollection();
				$current_cat = Mage::getModel('cmspro/category')->load($model->getId());
				$new_level = 1; $root_path1 = "";
				$old_level = $current_cat->getLevel();
				//if parent_id is exist:
		  		if($current_cat->getParentId()!="0"){
		  			$parent = Mage::getModel('cmspro/category')->load($current_cat->getParentId());
		  			$new_level = intval($parent->getLevel()) + 1;
					$root_path1 = $parent->getRootPath();
		  		}

				$root_path = $root_path1.$model->getId()."/" ;
		  		$diff_level = $new_level - intval($old_level);
		  		$current_cat = Mage::getModel('cmspro/category')->load($model->getId());
				if($current_cat->getRootPath()==""){
					$current_cat->setRootPath($root_path);
					$sql = "UPDATE ".$collections->getTable('category')." 
			  				SET  
								`level` = '".$new_level."',
								`root_path` = '".$root_path."'
							WHERE
								`category_id` = ".$model->getId();
							
				}else{
			  		$sql = "UPDATE ".$collections->getTable('category')." 
			  				SET  
								`level` = ".$diff_level." +`level` ,
								`root_path` = REPLACE(`root_path`,'".$current_cat->getRootPath()."','".$root_path."'),
								`status` = '".$model->getStatus()."'
							WHERE 
								`root_path` LIKE '".$current_cat->getRootPath()."%'	
							";
				}
				$conn = Mage::getModel('core/resource')->getConnection('core_write');
				$conn->query($sql);
				
				//save category_store
				$catResource = Mage::getResourceModel('cmspro/category');
				$catResource->saveCategoryStores($model);
				
				//iterate through editing category and all descendant cat, get all news
				$newsIds = $this->getAllChildrensNews($model->getId());
				foreach ($newsIds as $newsId) {
					//update table news_store
					$storeIds = array();
					$selectQuery =  "SELECT DISTINCT cs.store_id FROM ".$catResource->getTable('cmspro/category_store')." AS cs 
					INNER JOIN ".$catResource->getTable('cmspro/news_category')." AS ns ON cs.category_id = ns.category_id
					WHERE ns.news_id ='".$newsId."'";
			   		$deleteQuery = "DELETE FROM ".$catResource->getTable('cmspro/news_store')." WHERE news_id ='".$newsId."'";
					$storeIds = $conn->fetchAll($selectQuery);
			   		$conn->query($deleteQuery);
			   		sort($storeIds);
			   		foreach ($storeIds as $storeId) {
				   		$insertQuery = "INSERT INTO ".$catResource->getTable('cmspro/news_store');
				   		$insertQuery .= " VALUES (".$newsId.",".$storeId["store_id"].")";
				   		$conn->query($insertQuery);
				   		if($storeId["store_id"] == 0) break;
			   		}
			   		
			   		//update status
			   		$oldStatus = ($model->getStatus()==1) ? 2:1;
			   		$selectQuery1 = "SELECT c.category_id 
				   		FROM ".$catResource->getTable('cmspro/news')." AS n , ".$catResource->getTable('cmspro/news_category')." AS nc, ".$catResource->getTable('cmspro/category')." AS c
						WHERE n.news_id = nc.news_id AND
							nc.category_id = c.category_id AND
							c.`status` = ".$oldStatus." AND
							n.news_id = ".$newsId;
			   		$catId = $conn->fetchAll($selectQuery1);
			   		if(count($catId)<1){
			   			$news = Mage::getSingleton('cmspro/news')->load($newsId);
			   			$news->setStatus($model->getStatus());
			   			$news->save();
			   		}
				}
				
				// Update URL Rewrite
				$suffix = Mage::getStoreConfig('mw_cmspro/info/category_suffix') ? Mage::getStoreConfig('mw_cmspro/info/category_suffix'):".html"; 
		  		$url = "";
				$rq_path = "";
				$identifier = isset($data['identifier']) ? $data['identifier'] : $data['name'];
				if($identifier!=""){
					$rq_path = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format(Mage::getModel('cmspro/category')->getCurrentRoot()->getName()));
					$rq_path = strtolower($rq_path);
					$identifier = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($identifier));
					$identifier = strtolower($identifier);				
					$rq_path = $rq_path."/".$identifier;
				}else{
					//echo $current_cat->getRootPath();exit;
					$paths = explode('/',trim($current_cat->getRootPath()));
					foreach($paths as $key=>$parent){
							if($parent!=""){
								$parent = Mage::getModel('cmspro/category')->load($parent);
								if($parent->getName()!=""){
									$url = "";
									if($parent->getLevel()!='1' || $parent->getParentId()!='0'){
										$url = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($parent->getName()));
										$url = strtolower($url);
										$rq_path .= "/".$url;
									}else{
										$url = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format(Mage::getModel('cmspro/category')->getCurrentRoot()->getName()));
										$url = strtolower($url);
										$rq_path .=$url;
									}
								}
							}
						}
				}
				$rq_path1 = trim($rq_path).$suffix;				
		     	$url_rewrite['request_path'] = $rq_path1;
		     	$url_rewrite['target_path'] = 'cmspro/category/view/id/'.$model->getId();
		     	$url_rewrite['id_path'] = $rq_path1;
		     	$url_rewrite['is_system'] = 0;
		     	$url_rewrite['options'] = 0;

				$current_cat = Mage::getModel('cmspro/category')->load($model->getId());
		     	if( (!$current_cat->getUrlRewriteId()) OR $current_cat->getUrlRewriteId()=="0" ) {
		     		//checking duplicate request path when add news url
			     	$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
			     	if($url_model1->count()>0){
			     		$rq_path2 = $rq_path.rand(100,999999).$suffix;
			     		$url_rewrite['request_path'] = $rq_path2;
			     		$url_rewrite['id_path'] = $rq_path2;
			     	} 
			     	
			     	$url_model = Mage::getModel('core/url_rewrite');
					$url_model->setData($url_rewrite);
					$url_model->save();
					$model->setUrlRewriteId($url_model->getId());
					$model->save();
				}else{
					$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
					$dulicate = false;
					foreach($url_model1 as $u){
						if($u->getUrlRewriteId()!=$current_cat->getUrlRewriteId()) $dulicate = true;
					}
					if($dulicate){
			     		$rq_path2 = $rq_path.rand(100,999999).$suffix;
			     		$url_rewrite['request_path'] = $rq_path2;
			     		$url_rewrite['id_path'] = $rq_path2;
			     	}
			     	$url_model = Mage::getModel('core/url_rewrite')->load($current_cat->getUrlRewriteId());
					$url_model->setData($url_rewrite)->setId($current_cat->getUrlRewriteId());
					$url_model->save();
				}
				$model->reindexCategory();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cmspro')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/index');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cmspro')->__('Unable to find item to save'));
        $this->_redirect('*/*/index');
	}
 
	 public function deleteAction() {
	 	//echo($this->getRequest()->getParam('id')); exit;
		if( $this->getRequest()->getParam('id') > 0 ) {
			if($this->getRequest()->getParam('id') != 1){
				try {
					$root_path = Mage::getModel('cmspro/category')->load($this->getRequest()->getParam('id'))->getRootPath();
					$collections = Mage::getModel('cmspro/category')->getCollection();
					$collections->getSelect()->where("main_table.root_path LIKE '".$root_path."%'");
					
					foreach($collections as $cat){
						// Delete url_rewrite:
						Mage::getModel('core/url_rewrite')->setId($cat->getUrlRewriteId())->delete();
						
						// Delete store category:
						$delStore = "DELETE FROM `".$collections->getTable('category_store')."` WHERE `category_id`='".$cat->getCategoryId()."'";
						$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
						$conn->query($delStore);
						
						//update category to root for all news of deleting category...
						try {
							$collections = Mage::getModel('cmspro/category')->getCollection();
							$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
							$updateCat = "UPDATE `".$collections->getTable('cmspro/news_category')."`";
							$updateCat .= " SET `category_id`='1'";
							$updateCat .= " WHERE `category_id`='".$cat->getId()."'";
							$conn->query($updateCat);
						}catch(Exception $e)
						{
							Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						}
						// Delete category:
						Mage::getModel('cmspro/category')->setId($cat->getCategoryId())->delete();
					}
					
					//...then update store for all news that category were changed
					$lastUpdateIds = $conn->fetchAll("SELECT news_id FROM ".$collections->getTable('cmspro/news_category')." WHERE `category_id`='1';");
					foreach ($lastUpdateIds as $id)
					{
						$deleteQuery = "DELETE FROM ".$collections->getTable('cmspro/news_store')." WHERE news_id ='".$id["news_id"]."'";
						$conn->query($deleteQuery);
						$insertQuery = "INSERT INTO `".$collections->getTable('cmspro/news_store')."`";
						$insertQuery .= " VALUES(".$id["news_id"].",'0')";
						$conn->query($insertQuery);
					}
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
					$this->_redirect('*/*/index');
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('edit', array('id' => $this->getRequest()->getParam('id')));
				}
			}else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('You can not delete root category!'));
				$this->_redirect('edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/index');
	}

    public function massDeleteAction() {
        $categoryIds = $this->getRequest()->getParam('category');
        if(!is_array($categoryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                	if($categoryId != 1)
                	{
	                	$current_cat = Mage::getModel('cmspro/category')->load($categoryId);
	                	if($current_cat){
		                    $root_path = $current_cat->getRootPath();
							$collections = Mage::getModel('cmspro/category')->getCollection();
							$collections->getSelect()->where("main_table.root_path LIKE '".$root_path."%'");
							
							foreach($collections as $cat){
							// Delete url_rewrite:
							Mage::getModel('core/url_rewrite')->setId($cat->getUrlRewriteId())->delete();
							
							// Delete store category:
							$delStore = "DELETE FROM `".$collections->getTable('category_store')."` WHERE `category_id`='".$cat->getCategoryId()."'";
							$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
							$conn->query($delStore);
							
							//update category to root for all news of deleting category...
							try {
								$collections = Mage::getModel('cmspro/category')->getCollection();
								$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
								$updateCat = "UPDATE `".$collections->getTable('cmspro/news_category')."`";
								$updateCat .= " SET `category_id`='1'";
								$updateCat .= " WHERE `category_id`='".$cat->getId()."'";
								$conn->query($updateCat);
							}catch(Exception $e)
							{
								Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
							}
							// Delete category:
							Mage::getModel('cmspro/category')->setId($cat->getCategoryId())->delete();
							}
						
							//...then update store for all news that category were changed
							$lastUpdateIds = $conn->fetchAll("SELECT news_id FROM ".$collections->getTable('cmspro/news_category')." WHERE `category_id`='1';");
							foreach ($lastUpdateIds as $id)
							{
								$deleteQuery = "DELETE FROM ".$collections->getTable('cmspro/news_store')." WHERE news_id ='".$id["news_id"]."'";
								$conn->query($deleteQuery);
								$insertQuery = "INSERT INTO `".$collections->getTable('cmspro/news_store')."`";
								$insertQuery .= " VALUES(".$id["news_id"].",'0')";
								$conn->query($insertQuery);
							}
	                	}
                	}
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
    public function setCategoryOrderAction() {
    	
	    $fieldId = (int) $this->getRequest()->getParam('id');
	    $value = $this->getRequest()->getParam('value');
	    if ($fieldId) {
	        $model = Mage::getModel('cmspro/category')->load($fieldId)->setOrderCat($value)->save();
	    }
    } 

    public function setOrderAction() {
        $params = $this->getRequest()->getParam('items');
        if(!$params) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
            	$params = explode('|',$params);
                foreach ($params as $param) {
					//echo $this->getRequest()->getPost('order'.$categoryId); exit;
					$param = explode('-',$param);
					if(sizeof($param)>1){
						$model = Mage::getModel('cmspro/category');
						$model->setData(array('order_cat'=>$param[1]))->setId($param[0]);
						$model->save();
					}
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($params)
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
    	$count = 0; 
        $categoryIds = $this->getRequest()->getParam('category');
        if(!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                	$catResource = Mage::getResourceModel('cmspro/category');
                	$catIds = $catResource->getChildrenIds($categoryId);
                	foreach ($catIds as $catId){
	                    $category = Mage::getSingleton('cmspro/category')
	                        ->load($catId);
	                        if( $category->hasStatusChange($this->getRequest()->getParam('status')) )
	                        {
		                        $category->setStatus($this->getRequest()->getParam('status'))
		                        ->setIsMassupdate(true)
		                        ->save();
		                        $count++;
		                        $conn = Mage::getModel('core/resource')->getConnection('core_write');
		                        //update status for current category's news
								$newsIds = $this->getAllChildrensNews($catId);
		                        foreach ($newsIds as $newsId) {
							   		$oldStatus = ($this->getRequest()->getParam('status')==1) ? 2:1;
							   		$selectQuery1 = "SELECT c.category_id 
								   		FROM ".$catResource->getTable('cmspro/news')." AS n , ".$catResource->getTable('cmspro/news_category')." AS nc, ".$catResource->getTable('cmspro/category')." AS c
										WHERE n.news_id = nc.news_id AND
											nc.category_id = c.category_id AND
											c.`status` = ".$oldStatus." AND
											n.news_id = ".$newsId;
							   		$catId = $conn->fetchAll($selectQuery1);
							   		if(count($catId)<1){
							   			$news = Mage::getSingleton('cmspro/news')->load($newsId);
							   			$news->setStatus($this->getRequest()->getParam('status'));
							   			$news->save();
							   		}
		                        }
	                        }
                	}
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $count)
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
        $content    = $this->getLayout()->createBlock('cmspro/adminhtml_category_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'cmspro.xml';
        $content    = $this->getLayout()->createBlock('cmspro/adminhtml_category_grid')
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
    
	public function reindexAction(){
    	try{
    		$category = Mage::getModel('cmspro/category');
    		$category->reindexCategory();
    		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('All Categories were reindexed Successful'));
    	}catch(Exception $e){
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occured, please try again'));
    	}
    	$this->_redirect('*/*/index'); 
    }
    
	//iterate through a category and all of it's descendant cat, get all news
    public function getAllChildrensNews($catId){
    	$newsIdsArray = array();
    	$catResource = Mage::getResourceModel('cmspro/category');
		$catIdsArray = $catResource->getChildrenIds($catId);
		foreach($catIdsArray as $id){
			$newsCollection = Mage::getModel('cmspro/news')->getCollection();
			$newsCollection->addFieldToSelect('news_id');
			$newsCollection->getSelect()->join(array('nc'=>$catResource->getTable('cmspro/news_category')),
											  'main_table.news_id = nc.news_id',array(''))
										->where('nc.category_id = '.$id);
			$newsIdsArray = array_merge($newsIdsArray,$newsCollection->getAllIds());
		}
		return array_unique($newsIdsArray);
    }
}