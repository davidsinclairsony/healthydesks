<?php

class MW_Cmspro_Block_Adminhtml_Comment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('comment_form', array('legend'=>Mage::helper('cmspro')->__('Comment information')));
		
	 
	$fieldset->addField('fullname', 'text', array(
		'label'     => Mage::helper('cmspro')->__('Fullname'),
		 'class'     => 'required-entry',
		 'required'  => true,
          'name'      => 'fullname'
      ));
	  
	  $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('cmspro')->__('Email'),
          'required'  => true,
          'name'      => 'email',
	  ));
	  
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('cmspro')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('cmspro')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('cmspro')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('cmspro')->__('Content'),
          'title'     => Mage::helper('cmspro')->__('Content'),
          'style'     => 'width:280px; height:250px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCommentData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCommentData());
          Mage::getSingleton('adminhtml/session')->setCommentData(null);
      } elseif ( Mage::registry('comment_data') ) {
          $form->setValues(Mage::registry('comment_data')->getData());
      }
      return parent::_prepareForm();
  }
}