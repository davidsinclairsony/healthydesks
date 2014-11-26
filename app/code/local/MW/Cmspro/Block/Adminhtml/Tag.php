<?php
class MW_Cmspro_Block_Adminhtml_Tag extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
    	$this->_blockGroup = 'cmspro';
        $this->_controller = 'adminhtml_tag';
        $this->_headerText = Mage::helper('cmspro')->__('Manage Tags');
        $this->_addButtonLabel = Mage::helper('cmspro')->__('Add New Tag');
        parent::__construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-tag';
    }
}