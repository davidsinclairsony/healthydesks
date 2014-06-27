<?php 

class MW_Cmspro_Block_Adminhtml_Category_Edit_Tab_Restriction extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('meta_form', array('legend'=>Mage::helper('cmspro')->__('Meta information')));
     $collection= Mage::getSingleton('customer/group')->getCollection();$values=array();
	  foreach($collection as $group){
		$value=array();
		$value['label']= $group->getCustomerGroupCode();
		$value['value']=$group->getCustomerGroupId();
		$values[]=$value;
	 }; 
  $fieldset->addField('guser', 'multiselect', array(
	                'name'      => 'guser',
	                'label'     => Mage::helper('cmspro')->__('Groups Restriction'),
	                'title'     => Mage::helper('cmspro')->__('Groups Restriction'),
	                'required'  => false,
	                'values'    =>$values ,
	            )); 
    $fieldset->addField('users', 'editor', array(
          'name'      => 'users',
          'label'     => Mage::helper('cmspro')->__('User Restriction'),
          'title'     => Mage::helper('cmspro')->__('User Restriction'),
          'style'     => 'width:600px; height:150px;',
          'wysiwyg'   => false,
      ));
	
	if ( Mage::getSingleton('adminhtml/session')->getCategoryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
          Mage::getSingleton('adminhtml/session')->setCategoryData(null);
      } elseif ( Mage::registry('cmsprocategory_data') ) {
          $form->setValues(Mage::registry('cmsprocategory_data')->getData());
          if (!Mage::app()->isSingleStoreMode()) {
			  $catc = Mage::registry('cmsprocategory_data');
			  if($catc->getCategoryId()){     	  	
				// get array of selected groups of users; 					
	$arrgroup =explode(",",$catc->getGroups());$i=0;
					foreach($arrgroup as $groupid){													
						if($groupid==null||$groupid==""||$groupid==" ") 
						{
						unset($arrgroup[$i]);
						break;	
						};
							$i++;
					}	
			$arruser =explode(",",$catc->getUsers());$i=0;		 
					foreach($arruser as $userid){													
						if($userid==null||$userid==""||$userid==" ") 
						{
						unset($arrgroup[$i]);
						break;	
						};
							$i++;
					}
					 $collection= Mage::getSingleton('customer/customer')->getCollection();
					$collection->addFieldToFilter('entity_id',array('in' => $arruser));$str="";
					foreach ($collection as $user){
						$str=$str.$user->getEmail().",";
					};
					// set value for store view selected:
					$form->getElement('guser')->setValue($arrgroup); 
					$form->getElement('users')->setValue($str); 
				
			 }
		 }
      }
	  
	  
      return parent::_prepareForm();
  }
}