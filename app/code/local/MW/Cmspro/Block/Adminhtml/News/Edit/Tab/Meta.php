<?php

class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('meta_form', array('legend'=>Mage::helper('cmspro')->__('Meta information')));
      
      $fieldset->addField('page_title', 'text', array(
          'label'     => Mage::helper('cmspro')->__('Page Title'),
          'name'      => 'page_title',
      	  'style'	  => 'width:600px;',
      ));
      
      $fieldset->addField('meta_keyword', 'editor', array(
          'name'      => 'meta_keyword',
          'label'     => Mage::helper('cmspro')->__('Meta Keyword'),
          'title'     => Mage::helper('cmspro')->__('Meta Keyword'),
          'style'     => 'width:600px; height:150px;',
          'wysiwyg'   => false,
      ));
      
      $fieldset->addField('meta_description', 'editor', array(
          'name'      => 'meta_description',
          'label'     => Mage::helper('cmspro')->__('Meta Description'),
          'title'     => Mage::helper('cmspro')->__('Meta Description'),
          'style'     => 'width:600px; height:150px;',
          'wysiwyg'   => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCategoryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
          Mage::getSingleton('adminhtml/session')->setCategoryData(null);
      } elseif ( Mage::registry('cmspronews_data') ) {
          $form->setValues(Mage::registry('cmspronews_data')->getData());
      }
      return parent::_prepareForm();
  }

    public function getTabLabel(){
    	return Mage::helper('cmspro')->__('Meta');
    }
    public function getTabTitle(){
    	return Mage::helper('cmspro')->__('Meta');
    }
    public function canShowTab(){
    	return true;
    }
    public function isHidden(){
    	return false;
    }
    
}