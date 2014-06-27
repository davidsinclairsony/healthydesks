<?php

class MW_Cmspro_Block_Adminhtml_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('comment_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cmspro')->__('Comment Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cmspro')->__('Comment Information'),
          'title'     => Mage::helper('cmspro')->__('Comment Information'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_comment_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}