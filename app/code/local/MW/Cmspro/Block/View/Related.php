<?php
class MW_Cmspro_Block_View_Related extends Mage_Catalog_Block_Product_Abstract
{
    protected $_itemCollection;

	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}
	
    protected function _prepareData()
    {
		$this->_itemCollection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addStoreFilter($this->getStore());
		$relationTableName = $this->_itemCollection->getTable('cmspro/relation');
		$this->_itemCollection->getSelect()
			 ->join(array('relation'=>$relationTableName), 'relation.entity_id = e.entity_id')
			 ->where('relation.new_id='.$this->getRequest()->getParam('id') )	;		 
			
			$this->_itemCollection->setPageSize(Mage::getStoreConfig('mw_cmspro/product/number_related_product') ? Mage::getStoreConfig('mw_cmspro/product/number_related_product'):5);
			
        /* @var $product Mage_Catalog_Model_Product */

        //$this->_itemCollection = $collection;
		
         Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
             Mage::getSingleton('checkout/session')->getQuoteId()
         );
      	//$this->_addProductAttributesAndPrices($collection);

        //Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        //Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
		//add filter visible in catalog and enable(Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection is deprecated)
        $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
		$this->_itemCollection->addAttributeToFilter('visibility', $visibility);
		$this->_itemCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		//sort by position
//		$this->_itemCollection-
		$this->_itemCollection->getSelect()->order('relation.position'); 
		//echo $this->_itemCollection->getSelect();die;
		$this->_itemCollection->load();
        foreach ($this->_itemCollection as $product) {
       //     $product->setDoNotUseCategoryId(true);
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
