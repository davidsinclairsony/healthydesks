<?php 

class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Restriction extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('meta_form', array('legend'=>Mage::helper('cmspro')->__('User Restriction')));
     $collection= Mage::getSingleton('customer/group')->getCollection();$values=array();
	  foreach($collection as $group){
		$value=array();
		$value['label']= $group->getCustomerGroupCode();
		$value['value']=$group->getCustomerGroupId();
		$values[]=$value;
	 }; 
	 //add "all" item for group
	 $values[]= array('label'=>'All Group','value'=>'all');
	 
  $fieldset->addField('guser', 'multiselect', array(
	                'name'      => 'guser',
	                'label'     => Mage::helper('cms')->__('Groups Restriction'),
	                'title'     => Mage::helper('cms')->__('Groups Restriction'),	               
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
	
	if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
         $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('cmspronews_data') ) {
          $form->setValues(Mage::registry('cmspronews_data')->getData());
          $news = Mage::registry('cmspronews_data');
		  
			 if($news->getNewsId()){  	
				// get array of selected groups of users; 					
	$arrgroup =explode(",",$news->getGroups());$i=0;
					foreach($arrgroup as $groupid){													
						if($groupid==null||$groupid==""||$groupid==" ") 
						{
						unset($arrgroup[$i]);						
						};
							$i++;
					}	
			$arruser =explode(",",$news->getUsers());$i=0;		 
					foreach($arruser as $userid){													
						if($userid==null||$userid==""||$userid==" ") 
						{
						unset($arrgroup[$i]);					
						};
							$i++;
					}
					 $collection= Mage::getSingleton('customer/customer')->getCollection();
					$collection->addFieldToFilter('entity_id',array('in' => $arruser));$str="";
					foreach ($collection as $user){
						$str=$str.$user->getEmail().",";
					};
					//Zend_Debug::dump($str);die();
					// set value for store view selected:
					$form->getElement('guser')->setValue($arrgroup); 
					$form->getElement('users')->setValue($str); 
			 }else $form->getElement('guser')->setValue(array('all'));
		 }
        
	  
      return parent::_prepareForm();
  }
}