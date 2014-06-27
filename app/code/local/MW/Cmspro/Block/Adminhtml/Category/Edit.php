<?php

class MW_Cmspro_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {	
	 	parent::__construct();  
        $this->_objectId = 'id';
        $this->_blockGroup = 'cmspro';
        $this->_controller = 'adminhtml_category';
        
//        $this->setTemplate('catalog/category/edit.phtml');
        
        $this->_updateButton('save', 'label', Mage::helper('cmspro')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('cmspro')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cmsprocategory_data') && Mage::registry('cmsprocategory_data')->getId() ) {
            return Mage::helper('cmspro')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('cmsprocategory_data')->getName()));
        } else {
            return Mage::helper('cmspro')->__('Add Category');
        }
    }
}