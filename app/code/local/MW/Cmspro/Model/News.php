<?php

class MW_Cmspro_Model_News extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        //$this->_eventPrefix = 'cmspro_news';
        $this->_init('cmspro/news');
    }
	
    public function reindexNews(){
    	$category = Mage::getModel('cmspro/category');
    	$category->createRoot();
		$suffix = Mage::getStoreConfig("mw_cmspro/info/news_suffix") ? Mage::getStoreConfig("mw_cmspro/info/news_suffix") : ".html";
		foreach(Mage::getModel('cmspro/news')->getCollection() as $news){
			$news = Mage::getModel("cmspro/news")->load($news->getId());
			$url_cat = Mage::getModel('core/url_rewrite')->load($news->getUrlRewriteId());
			$url = "";
			$rq_path = "";
			if($news->getIdentifier()){
				$url_key = $news->getIdentifier();
			}else{
				$url_key = $news->getTitle();
			}
			$root = Mage::helper('cmspro')->makeStringFriendly(($this->getRootNameFromConfig()));
			
			$url_key = Mage::helper('cmspro')->makeStringFriendly(($url_key));
			
			$rq_path = $root."/".$url_key;
			$rq_path1 = trim($rq_path, '-').$suffix;
			
			$url_model1 = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',$rq_path1);
			$dulicate = false;
			foreach($url_model1 as $u){
				if($u->getUrlRewriteId()!=$news->getUrlRewriteId()) $dulicate = true;
			}
			$rq_path2 = "";
			if($dulicate){
	     		$rq_path2 = $rq_path.rand(100,999999).$suffix;
	     	}else{
	     		$rq_path2 = $rq_path1;
	     	}
			$data = array(
					'url_rewrite_id'=> $url_cat->getUrlRewriteId(),
					'request_path' => $rq_path2,
					'target_path'  => 'cmspro/view/details/id/'.$news->getId(),
					'id_path'	   => $rq_path2,
					'is_system'	   => 1,
					'options'	   => 0,
				);
			
	     	$url_cat->setData($data);
	     	$url_cat->save();
	     	$news->setUrlRewriteId($url_cat->getId());
	     	$news->save();
		}
	}
	
	public function getRootNameFromConfig(){
    	return Mage::getStoreConfig('mw_cmspro/news/root_cmspro_name') ? Mage::getStoreConfig('mw_cmspro/news/root_cmspro_name'):"news";
    }
}