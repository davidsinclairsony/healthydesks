<?php
class MW_Cmspro_Block_Preview_Related extends Mage_Catalog_Block_Product_Abstract
{
    protected $_itemCollection;

	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}
	
    protected function _prepareData()
    {
    	$data = $this->getRequest()->getPost();
    	$links = $this->getRequest()->getPost('links');
    	
		$this->_itemCollection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addStoreFilter($this->getStore());
			
		if(!isset($links['related']) && $this->getRequest()->getParam('id')){
			$relationTableName = $this->_itemCollection->getTable('cmspro/relation');
			$this->_itemCollection->getSelect()
			 	->join(array('relation'=>$relationTableName), 'relation.entity_id = e.entity_id')
			 	->where('relation.new_id='.$this->getRequest()->getParam('id') );
		}else{
			$array_related_products = array();
			$array_related_products = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
			$this->_itemCollection->addAttributeToFilter('entity_id',array('in'=>$array_related_products));
		}

		$this->_itemCollection->setPageSize(Mage::getStoreConfig('mw_cmspro/product/number_related_product') ? Mage::getStoreConfig('mw_cmspro/product/number_related_product'):5);
			
        /* @var $product Mage_Catalog_Model_Product */

        //$this->_itemCollection = $collection;
		
         Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
             Mage::getSingleton('checkout/session')->getQuoteId()
         );
         
        $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
		$this->_itemCollection->addAttributeToFilter('visibility', $visibility);
		$this->_itemCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		
		//sort by position
		//$this->_itemCollection->getSelect()->order('relation.position'); 
	
		$this->_itemCollection->load();
        foreach ($this->_itemCollection as $product) {

        }
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
}
