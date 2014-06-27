<?php

class MW_Cmspro_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('category_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cmspro')->__('Category Information'));
  }
  /**
   * Load Wysiwyg on demand and Prepare layout
   */	
  protected function _prepareLayout()
  {
  	$version = Mage::getVersion();
        if(version_compare($version, '1.4.0.1', '>=')===true){
	        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
	            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
	        }
        }
	return parent::_prepareLayout();
  }
    
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cmspro')->__('General'),
          'title'     => Mage::helper('cmspro')->__('General'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_category_edit_tab_general')->toHtml(),
      ));
	     /* $this->addTab('restric', array(
          'label'     => Mage::helper('cmspro')->__('User Restriction'),
          'title'     => Mage::helper('cmspro')->__('User Restriction'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_category_edit_tab_restriction')->toHtml(),
      )); */
     $this->addTab('aaaa', array(
          'label'     => Mage::helper('cmspro')->__('Meta Information'),
          'title'     => Mage::helper('cmspro')->__('Meta Information'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_category_edit_tab_meta')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}