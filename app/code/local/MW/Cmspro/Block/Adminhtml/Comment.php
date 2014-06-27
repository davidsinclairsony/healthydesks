<?php
class MW_Cmspro_Block_Adminhtml_Comment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_comment';
		$this->_blockGroup = 'cmspro';
		$this->_headerText = Mage::helper('cmspro')->__('Manage Comments');
		 /*  $this->_addButtonLabel = Mage::helper('cmspro')->__('Add Comment'); */
    	parent::__construct();
		$this->removeButton('add');
	}
	
}