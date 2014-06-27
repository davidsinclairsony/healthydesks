<?php
class MW_Cmspro_Block_Adminhtml_News extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	  public function __construct()
	  {
	    $this->_controller = 'adminhtml_news';
	    $this->_blockGroup = 'cmspro';
	    $this->_headerText = Mage::helper('cmspro')->__('Manage News');
	    $this->_addButton('reindex', array(
	            'label'     => Mage::helper('cmspro')->__('Reindex News'),
	            'onclick'   => 'setLocation(\'' . $this->getReindexUrl() .'\')',
				'id'		=> 'reindex',
	        ));
	    $this->_addButtonLabel = Mage::helper('cmspro')->__('Add News');
	    parent::__construct();
	  }
  
	public function getReindexUrl()
    {
        return $this->getUrl('*/*/reindex');
    }
    
}