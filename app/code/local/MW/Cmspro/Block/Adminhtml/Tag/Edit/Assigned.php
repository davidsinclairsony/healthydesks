<?php
class MW_Cmspro_Block_Adminhtml_Tag_Edit_Assigned extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add Assigned products accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('cmspro_current_tag')->getId())) {
            return $this;
        }

        $tagModel = Mage::registry('cmspro_current_tag');
		
        //disabled tag cannot assigned to news
        if( (!$tagModel->getStatus()) OR ($tagModel->getStatus() == 0) ){
        	return $this;
        }
        
        $this->setId('cmspro_tag_assigned_grid');

        $this->addItem('cmspro_tag_assign', array(
            'title'         => Mage::helper('cmspro')->__('News Tagged'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/assigned', array('ret' => 'all', 'tag_id'=>$tagModel->getId(), 'store'=>$tagModel->getStoreId())),
        ));
        return parent::_prepareLayout();
    }	
} 