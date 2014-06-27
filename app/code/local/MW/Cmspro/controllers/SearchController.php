<?php
class MW_Cmspro_SearchController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->_redirect('/');
    }
    
    public function resultAction(){
    	$this->loadLayout(); 
        /*if ($this->getRequest()->get('keyword')) {
        	$keyword = $this->getRequest()->get('keyword');
			$collection = Mage::getModel('cmspro/news')->getCollection()->setOrder('created_time','desc');
			$collection->getSelect()
				->join(
					array('store_table'=>'cmspro_news_store'),
					'store_table.news_id = main_table.news_id AND store_table.store_id in (0,'.Mage::app()->getStore()->getId().')',
					array()
				)
				->where(
					"main_table.title LIKE '%".$keyword."%' OR 
					 main_table.summary LIKE '%".$keyword."%' OR 
					 main_table.content LIKE '%".$keyword."%'
					");
			Mage::register('result_search_news',$collection);
        }*/
        $this->renderLayout();
    }
}