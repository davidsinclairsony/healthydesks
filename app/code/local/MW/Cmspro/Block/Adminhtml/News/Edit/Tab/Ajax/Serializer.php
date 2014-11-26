<?php
class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Ajax_Serializer extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/edit/serializer.phtml');
        return $this;
    }

    public function getProductsJSON()
    {
        $result = array();
        if ($this->getProducts()) {
            $isEntityId = $this->getIsEntityId();
            foreach ($this->getProducts() as $product) {
                $id = $isEntityId ? $product->getEntityId() : $product->getId();
                $result[$id] = $product->toArray(array('qty', 'position'));
            }
        }
        return $result ? Zend_Json_Encoder::encode($result) : '{}';
    }

    /**
     * Initialize grid block under the "Related Products", "Up-sells", "Cross-sells" sections
     *
     * @param string $blockName
     * @param string $getProductFunction
     * @param string $inputName
     */
    public function initSerializerBlock($blockName, $getProductFunction, $inputName)
    {
        if ($block = $this->getLayout()->getBlock($blockName)) {
            $this->setGridBlock($block)
                ->setProducts(Mage::registry('current_product')->$getProductFunction())
                ->setInputElementName($inputName);
        }
    }
}
