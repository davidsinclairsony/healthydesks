<?php
			
class MW_Cmspro_Model_Category extends Mage_Core_Model_Abstract
{
	protected $_itemLevelPositions = array();
	protected $treeCat = array();
	protected $treeCatObj=array();

    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/category');
        //$this->changeRoot();
    }
    
    /**
     * Check whether status has change or not
     */
    public function hasStatusChange($status){
    	if($status == $this->getStatus()){
 			return false;
    	}
    	return true;
    }
    
    
    /*
     * get all categoris level deferent  level 1
     * */
    
	public function getAllCategorySitemap($storeId=null){
		if(!$storeId) {
			$storeId = Mage::app()->getStore()->getId();	
		}
		
		$collection = $this->getCollection()
			->addFieldToFilter('level',array('nin'=>array('1')))
			->addFieldToFilter('status',"1")
			->setOrder('order_cat','asc');
			
		$collection->getSelect()->join(
			array('store'=>$collection->getTable('category_store')),
			'main_table.category_id = store.category_id',
			array('store.store_id')
		)->where('store.store_id in (?)',array('0',$storeId));
		
		return $collection;
	}
    /**
     * Get all categoris 
     */
    public function getRootCategory(){
    	$collection = $this->getCollection()
    		->addFieldToFilter('level',2)
    		->addFieldToFilter('status',"1")
    		->setOrder('order_cat','asc');
 		return $collection;   	
    }
	
    /*
     * Get all categories level 1 of current store and status on
     * */
	public function getMainCategories(){
		$collection = $this->getCollection()
			->addFieldToFilter('level',"1")
			->addFieldToFilter('status',"1")
			->setOrder('order_cat','asc');
	/* 	 $collection->getSelect()->join(
			array('store'=>$collection->getTable('category_store')),
			'main_table.category_id = store.category_id',
			array('store.store_id')
		)->where('store.store_id in (?)',array('0',Mage::app()->getStore()->getId())); */
		//echo $collection->getSelect();die;
		return $collection;
	}
	
	public function getChildren($catId=null){
		if(!$catId) {
			$catId = $this->getCategoryId();	
		}
		$childrens = $this->getCollection()
		->addFieldToFilter('status',"1")
		->addFieldToFilter('parent_id',$catId)->setOrder('order_cat','asc');
	 	 $childrens->getSelect()->join(
			array('store'=>$childrens->getTable('category_store')),
			'main_table.category_id = store.category_id',
			array('store.store_id')
		)->where('store.store_id in (?)',array('0',Mage::app()->getStore()->getId())); 
		//echo $collection->getSelect();die;
    	return $childrens;
    }
	 
    /**
     * *<li class="level0 nav-1 parent" onmouseout="toggleMenu(this,0)" onmouseover="toggleMenu(this,1)">
			<a href="#"><span>Furniture</span></a>
			<ul class="level0">
				<li class="level1 nav-1-1"><a href="#"><span>Living Room</span></a></li>
				<li class="level1 nav-1-2 last"><a href="#"><span>Bedroom</span></a></li>
			</ul>
		</li>
     *
     * @param unknown_type $category
     * @param unknown_type $level
     * @param unknown_type $last
     * @return unknown
     */
    public function drawItem($category, $level=1, $last=false,$current_cat_id="")
    {
        $html = '';
		
        $html.= '<li';
        
        $childs = $this->getChildren($category->getCategoryId());
        
        // Find Current Category
		
        if (sizeof($childs->getData())>0) {
             $html.= '';
        }
		
        $html.= ' class="level'.$level;
        $html.= ' nav-' .$this->_getItemPosition($level);
       
        if ($last) {
            $html .= ' last';
        }
        
        if (sizeof($childs->getData())>0) {
            $html .= ' parent';
        }

        //set active for category
    	if($current_cat_id){
    		$root_path = $this->load($current_cat_id)->getRootPath();
    		$root_path = explode('/',$root_path);
        	if(sizeof($root_path)>2){$current_cat_id = $root_path[1];}else{$current_cat_id="";}
        	if($current_cat_id==$category->getCategoryId()){ $html .=' active';}
    	}
 
        $requestpath = Mage::getModel('core/url_rewrite')->load($category->getUrlRewriteId())->getRequestPath();
        $html.= '">'."\n";
        $html.= '<a href="'.Mage::getBaseUrl().$requestpath.'"><span>'.$category->getName().'</span></a>'."\n";

        if (sizeof($childs->getData())>0){

            //$j = 0;
            $htmlChildren = '';
            foreach ($childs as $child) {
                if ($child->getStatus()) {
                	$objChild = Mage::getModel('cmspro/category')->load($child->getCategoryId());
                	//if($objChild->getChildren()){
                	$deeplevel = Mage::getStoreConfig("mw_cmspro/left_menu_category/deeplevel");
                	if($objChild->getLevel() <= $deeplevel){
                    	$htmlChildren.= $this->drawItem($objChild, $level+1);
                	}
                	//}else{
                		//$htmlChildren.= $this->drawItem($objChild, $level+1,true);
                	//}
                }
            }

            if (!empty($htmlChildren)) {
                $html.= '<ul class="level' . $level . '">'."\n"
                        .$htmlChildren
                        .'</ul>';
            }

        }
        $html.= '</li>'."\n";
        return $html;
    }
    
    public function _getItemPosition($level)
    {
        if ($level == 0) {
            $zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
            $this->_itemLevelPositions = array();
            $this->_itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->_itemLevelPositions[$level])) {
            $this->_itemLevelPositions[$level]++;
        } else {
            $this->_itemLevelPositions[$level] = 1;
        }

        $position = array();
        for($i = 0; $i <= $level; $i++) {
            if (isset($this->_itemLevelPositions[$i])) {
                $position[] = $this->_itemLevelPositions[$i];
            }
        }
        return implode('-', $position);
    }
    
   
    public function _getUrlRewrite(){
    	return Mage::getModel('core/url_rewrite')->load($this->getUrlRewriteId())->getRequestPath();
    }
	public function getAllChildren(){
    	$childrens = $this->getCollection()->setOrder('level','asc');
    
    	$childrens->getSelect()->join(
    		array('store_table'=>$childrens->getTable('category_store')),
    		'store_table.category_id = main_table.category_id',
    		array()
    		)->where("store_table.store_id in (?) AND root_path LIKE '".$this->getRootPath()."'",array('0',Mage::app()->getStore()->getId()));
    	return $childrens;
    } 
    
    public function getRootCategoryId($storeId){
    	//get root category by store:
    	$rootCat = Mage::getModel('cmspro/category')->getCollection();
    	$rootCat->getSelect()->join(
    		array('store_table'=>$rootCat->getTable('category_store')),
    		'store_table.category_id = main_table.category_id',
    		array()
    		)->where('store_table.store_id in (?)',array('0',$storeId));
    	if($rootCat){
    		return $rootCat->getCategoryId();
    	}else{
    		return 1;
    	}
    }
    /**
     * 
     * Get all categories like a tree
     * @param boolean $isRoot
     * @param boolean $status
     * return an array of Categories
     */
   
    public function getTreeCategory($isRoot=false,$status=true,$collection=NULL){
    	if($isRoot){
    		$this->treeCat[] = array('value'=>'1','label'=>'Root Category');
    	}
		if(!$collection){
			$collection = Mage::getModel('cmspro/category')->getCollection();
		}
//echo get_class($collection);
//echo $collection->getSelect().'<br/>';
		$filteredIds = $collection->getAllIds();
		$rootCat = Mage::getModel('cmspro/category')->getCollection();
    	if($status){
	    	$rootCat->addFieldToFilter('level',2)
		    		->addFieldToFilter('status',1)
		    		->addFieldToFilter('category_id',array("in"=>$filteredIds))
		    		->setOrder('order_cat','asc');
    	}else{
    		$rootCat->addFieldToFilter('level',2)
    				->addFieldToFilter('category_id',array("in"=>$filteredIds))
    				->setOrder('order_cat','asc');
    	}
    	foreach($rootCat as $cat){
    		$curcat = array('value'=>$cat->getCategoryId(),'label'=>$cat->getName());
    		$this->treeCat[] = $curcat;
    		$this->getChildCategory($cat,true,$collection);
    	}
    	return $this->treeCat;
    }
    public function getTreeCategoryAdmin(){
    	$rootCat = $this->getCollection();
    	$rootCat->addFieldToFilter('level',2)
    			->setOrder('order_cat','asc'); 
    	foreach($rootCat as $cat){
	    	$curcat = array('value'=>$cat->getCategoryId(),'label'=>$cat->getName());
	    	$this->treeCat[] = $curcat;
	    	$this->getChildCategory($cat,FALSE);
    	}
    	return $this->treeCat;
    }
    public function getChildCategory($category,$status=true,$collection=NULL){
    	if(!$collection){
			$collection = $this->getCollection();
		}
		$filteredIds = $collection->getAllIds();
		$childs = Mage::getModel('cmspro/category')->getCollection();
    	//echo $collection->getSelect();die;
    	if($status){
	    	$childs->addFieldToFilter('parent_id',$category->getCategoryId())
	    		   ->addFieldToFilter('category_id',array("in"=>$filteredIds))
	    		   ->addFieldToFilter('status',1)
	    		   ->setOrder('order_cat','asc');
    	}else{
    		$childs->addFieldToFilter('parent_id',$category->getCategoryId())
    			   ->addFieldToFilter('category_id',array("in"=>$filteredIds))
    			   ->setOrder('order_cat','asc');
    	}
//echo $childs->getSelect().'<br/>';
    	if( count($childs->getData())>0 ){
    		foreach($childs as $child){
    			//echo 'child: '.$child->getCategoryId().'<br/>';
    			$cat = array('value'=>$child->getCategoryId(),'label'=>$this->level($child->getLevel()).$child->getName());
    			$this->treeCat[] = $cat;
    			$cat = $this->getChildCategory($child,$status,$collection);
    		}
    	}
    }
    
    public function level($level){
    	$space = "";
    	for($i=0;$i<($level-2);$i++){ $space.=" -- "; }
    	return $space;
    }
     
    public function hasChildren(){
    	if($this->getId()){
    		$childs = $this->getCollection()->addFieldToFilter('parent_id',$this->getId());
    		if(sizeof($childs->getData())>0){
    			return true;
    		}else{
    			return false;
    		}
    	}
    	return false;
    }
    
	/*public function changeRoot(){ 
		$current_root = $this->load(1);
		$current_root->setName($this->getRootCmsproName());
		$current_root->save();
		$this->reindexCategory();
		$this->reindexNews();
	}*/
	
	public function reindexCategory(){
		
		$this->createRoot();
		$suffix = Mage::getStoreConfig("mw_cmspro/info/category_suffix") ? Mage::getStoreConfig("mw_cmspro/info/category_suffix") : ".html";
		$categories = $this->getCollection();
		foreach($this->getCollection() as $key=>$cat){
			
			$url_cat = Mage::getModel('core/url_rewrite')->load($cat->getUrlRewriteId());
			$url = "";
			$rq_path = "";
			if($cat->getIdentifier()){
				$rq_path = Mage::helper('cmspro')->makeStringFriendly($this->getRootNameFromConfig());
				
				$identifier = Mage::helper('cmspro')->makeStringFriendly($cat->getIdentifier());
								
				$rq_path = $rq_path."/".$identifier;
			}else{
				$paths = explode('/',trim($cat->getRootPath()));
				foreach($paths as $parent){
					if($parent!=""){
						$parent = Mage::getModel('cmspro/category')->load($parent);
						if($parent->getName()!=""){
							$url = "";
							if($parent->getLevel()!='1' || $parent->getParentId()!='0'){
								$url = Mage::helper('cmspro')->makeStringFriendly($parent->getName());
								
								$rq_path .= "/".$url;
							}else{
								$url = Mage::helper('cmspro')->makeStringFriendly($this->getRootNameFromConfig());
							
								$rq_path .=$url;
							}
						}
					}
				}
			}
			
			$rq_path1 = trim($rq_path).$suffix;

			$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
			
			$dulicate = false;
			if($url_model1->count()>0){ 
				foreach($url_model1 as $u){
					if($u->getUrlRewriteId()!=$cat->getUrlRewriteId()) $dulicate = true;
				}
			}
			$rq_path2 = "";
			if($dulicate)
	     		$rq_path2 = trim($rq_path).rand(100,999999).$suffix;
	     	else  
	     		$rq_path2 = $rq_path1;
	     	
	     	$target_path = "";
			if($cat->getLevel()=="1"):
				$target_path = 'cmspro/index/index';
			else:
				$target_path = 'cmspro/category/view/id/'.$cat->getCategoryId();
			endif;
			
			$data = array(
					'url_rewrite_id'=> $url_cat->getUrlRewriteId(),
					'request_path' => $rq_path2,
					'target_path'  => $target_path,
					'id_path'	   => $rq_path2,
					'is_system'	   => 0,
					'options'	   => 0,
				);
	     	$url_cat->setData($data);
	     	$url_cat->save();
	     	
	     	$cat->setUrlRewriteId($url_cat->getUrlRewriteId());
	     	$cat->save();
		}
		return true;
	}
	
	public function createRoot(){
	   	
		$root = $this->getCollection()->addFieldToFilter('level','1')->addFieldToFilter('parent_id','0');
    	if($root->count()==0){
			$data = array(
				'name' => $this->getRootNameFromConfig(),
				'created_time' => date('Y-m-d H:i:s'),
				'updated_time' => date('Y-m-d H:i:s'),
				'parent_id'	   => '0',
				'description'  => 'This is root Category',
				'order_cat'    => 1,
				'url_rewrite_id' => '0',
				'status' => '1'
			);
			$cat_root = Mage::getModel('cmspro/category');
	    	$cat_root->setData($data);
	    	$cat_root->save();
	    	$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
	    	$updateCat = "UPDATE `".$root->getTable('cmspro/category')."`";
			$updateCat .= " SET `category_id`='1'";
			$updateCat .= " WHERE `category_id`='".$cat_root->getId()."'";
	    	$conn->query($updateCat);
	    	$cat_root = Mage::getModel('cmspro/category')->load(1);
	    	$cat_root->setRootPath("1/");
	    	$cat_root->save();
	    	
	    	
	    	//update Url Rewrite for root category:
	    	$suffix = Mage::getStoreConfig("mw_cmspro/info/category_suffix") ? Mage::getStoreConfig("mw_cmspro/info/category_suffix") : ".html";
	    	$rq_path = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($this->getRootNameFromConfig()));
			$rq_path = strtolower($rq_path);
			$rq_path1 = trim($rq_path).$suffix;
			
	    	$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
			$rq_path2 = "";
			if($url_model1->count()>0){
	     		$rq_path2 = trim($rq_path).rand(100,999999).$suffix;
	     	}else{
	     		$rq_path2 = $rq_path1;
	     	}
		     	
			$data_rewrite = array(
						'request_path' => $rq_path2,
						'target_path'  => 'cmspro/index/index',
						'id_path'	   => $rq_path2,
						'is_system'	   => 0,
						'options'	   => 0,
					);
				
			$url_model = Mage::getModel('core/url_rewrite');
			$url_model->setData($data_rewrite);
	     	$url_model->save();
	     	$cat_root->setUrlRewriteId($url_model->getId());
	     	$cat_root->save();
    	}else{
	    	if($root->count()>0){
				foreach($root as $r){
					$r->setName($this->getRootNameFromConfig());
					$r->setPageTitle($this->getRootNameFromConfig());
					$r->setMetaDescription($this->getRootNameFromConfig());
					$r->setMetaKeyword($this->getRootNameFromConfig());
					$r->save();
				}
	    	}
    	}
    	return true;
    }
    
    public function getCurrentRoot(){
    	$root = $this->getCollection()->addFieldToFilter('level','1')->addFieldToFilter('parent_id','0');
    	$_root = Mage::getModel('cmspro/category');
    	if($root->count()>0){
			foreach($root as $r){
				$_root = $r;
			}
    	}
    	return $_root;
    }
    
    public function getRootNameFromConfig(){
    	return Mage::getStoreConfig('mw_cmspro/news/root_cmspro_name') ? Mage::getStoreConfig('mw_cmspro/news/root_cmspro_name'):"news";
    }
}