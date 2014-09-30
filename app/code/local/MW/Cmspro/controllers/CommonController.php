<?php

class MW_Cmspro_CommonController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction()
    {
    	$uri = explode('/cmspro/',$_SERVER['REQUEST_URI']);
		if(sizeof($uri)>1){
			$this->_forward('defaultNoRoute');
		}else{
			$this->loadLayout();
			$this->renderLayout();
		}
    	
    }
    
    public function viewAction(){
    	$this->common();	
    }
    
    public function detailsAction(){
    	$this->common();
    }
}