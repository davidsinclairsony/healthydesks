<?php
class MW_Cmspro_Block_Adminhtml_Tag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
     * Add and update buttons
     *
     * @return void
     */
    public function __construct()
    {
    	$this->_blockGroup = 'cmspro';
    	$this->_controller = 'adminhtml_tag';
        $this->_objectId   = 'tag_id';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('cmspro')->__('Save Tag'));
        $this->_updateButton('delete', 'label', Mage::helper('cmspro')->__('Delete Tag'));

        $this->addButton('save_and_edit_button', array(
            'label'   => Mage::helper('cmspro')->__('Save and Continue Edit'),
            'onclick' => "saveAndContinueEdit('" . $this->getSaveAndContinueUrl() . "')",
            'class'   => 'save'
        ), 1);
    }
	
	/**
     * Add child HTML to layout
     *
     * @return Mage_Adminhtml_Block_Tag_Edit
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('cmspro_tag_assign_accordion', $this->getLayout()->createBlock('cmspro/adminhtml_tag_edit_assigned'));
        return $this;
    }
    
	/**
     * Retrieve Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cmspro_current_tag')->getId()) {
            return Mage::helper('cmspro')->__("Edit Tag '%s'", $this->htmlEscape(Mage::registry('cmspro_current_tag')->getName()));
        }
        return Mage::helper('cmspro')->__('New Tag');
    }
    
    /**
     * Retrieve Tag Delete URL
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('tag_id' => $this->getRequest()->getParam($this->_objectId), 'ret' => $this->getRequest()->getParam('ret', 'index')));
    }
        
	/**
     * Retrieve Assigned Tags Accordion HTML
     *
     * @return string
     */
    public function getTagAssignAccordionHtml()
    {
        return $this->getChildHtml('cmspro_tag_assign_accordion');
    }
    
	/**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

	/**
     * Retrieve Tag SaveAndContinue URL
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'ret' => 'edit', 'continue' => $this->getRequest()->getParam('ret', 'index')));
    }
}