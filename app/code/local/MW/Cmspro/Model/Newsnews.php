<?php

class MW_Cmspro_Model_Newsnews extends Mage_Core_Model_Abstract
{
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/newsnews');
    }
	
	/*public function getRelatedNewsCollection(){
		$collection = Mage::getModel('cmspro/news_news')
				->getCollection();
		return $collection;
	}*/
	
	public function setRelatedNews($newsId, $arr){
	
		//prepare form data as $new array that hold editing relation data
		//Zend_Debug::dump($arr);die;
		$model = Mage::getResourceModel('cmspro/newsnews');
		$news = array();
		foreach ($arr as $key=>$val)
		{
			$news[$key] = $val['position'];
			
		}
		//Zend_Debug::dump($news);die;
		if($newsId  == 0){
			$newsId = 1;
		}
		 
		//$productIds = array_keys($arr);
		$model->processRelations($newsId, $news);
		//$model->setData($data)->setId($newId);
		//$model->save();
	}
	
}