<?php
class MW_Cmspro_Block_Comment extends Mage_Core_Block_Template{

	public function _prepareLayout()
    {	/* if(Mage::getStoreConfig('cmspro/news/using_comment')) */
		return parent::_prepareLayout();
	}

	

    public function getNewsId()
    {
		$param = $this->getRequest()->getParam('id');
		$id  =   Mage::getModel('cmspro/news')->load($param)->getId();
        return $id;
    }
	
	public function checkSession(){
		$session = Mage::getSingleton('customer/session');
		$cus = $session->getCustomerId();
		return $cus;
	}
	
	public function curPageURL() {
		 $pageURL = 'http';
		 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 return $pageURL;
	}
	
	public function isDisplayPhone(){
		 return Mage::getStoreConfig('mw_cmspro/comment/phone');
	}
	
	public function getFormAction(){
		return $this->getUrl('*/comment/post', null);
	}
	
	public function getPublicKey(){
		return Mage::getStoreConfig('mw_cmspro/comment/public_captcha_key');
	}

	  /**
     * Retrieve collection of ratings
     *
     * @return Mage_Rating_Model_Mysql4_Rating_Option_Vote_Collection
     */
    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }
   
}