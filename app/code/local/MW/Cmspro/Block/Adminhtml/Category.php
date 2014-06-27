<?php
class MW_Cmspro_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_category';
		$this->_blockGroup = 'cmspro';
		$this->_headerText = Mage::helper('cmspro')->__('Manage Categories');
		$this->_addButton('save', array(
            'label'     => Mage::helper('cmspro')->__('Save Order'),
            'onclick'   => 'save_order()',
			'id'		=> 'save_cat',
        ));
        $this->_addButton('reindex', array(
            'label'     => Mage::helper('cmspro')->__('Reindex Categories'),
            'onclick'   => 'setLocation(\'' . $this->getReindexUrl() .'\')',
			'id'		=> 'reindex',
        ));
        
        $this->_addButtonLabel = Mage::helper('cmspro')->__('Add Category');
    	parent::__construct();
	}
	
	public function getSaveOrderUrl()
    {
        return $this->getUrl('*/*/setOrder');
    }
    
    public function getReindexUrl(){
    	return $this->getUrl('*/*/reindex');
    }
}