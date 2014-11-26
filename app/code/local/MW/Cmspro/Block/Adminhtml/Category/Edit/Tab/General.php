<?php

class MW_Cmspro_Block_Adminhtml_Category_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('general_form', array('legend'=>Mage::helper('cmspro')->__('General information')));
//Mage::log(Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true));

	  if (!Mage::app()->isSingleStoreMode()) {
	            $fieldset->addField('store_id', 'multiselect', array(
	                'name'      => 'category_stores[]',
	                'label'     => Mage::helper('cms')->__('Store View'),
	                'title'     => Mage::helper('cms')->__('Store View'),
	                'required'  => true,
	                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
	            ));
	   }
	              
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('cmspro')->__('Category Name'),
          'required'  => true,
          'name'      => 'name',
      ));
      
      $id = $this->getRequest()->getParam('id');
      if($id){
	      $fieldset->addField('level_category', 'note', array(
	          'label'     => Mage::helper('cmspro')->__('Category Level'),
	          'required'  => false,
	          'name'      => 'level_category',
	          'text'      => Mage::getModel('cmspro/category')->load($id)->getLevel()
	      ));
      }
      
      //filter: a category can chosse itself or it's children as parent category.
      
      $collection =  Mage::getModel('cmspro/category')->getCollection();
      $collection->addFieldToFilter( 'category_id',array("nin"=>array($id)) )
      			 ->addFieldToFilter( 'parent_id',array("nin"=>array($id)) );
      $categories = Mage::getModel('cmspro/category')->getTreeCategory(true,true,$collection);
	  
      //get store for each category
      $JSONData = $this->_prepareParentCategoryJSON($categories);
      $categoriesData = $JSONData['cat_data'];
      
      //Zend_Debug::dump($categoriesData);die;
	  
	  $fieldset->addField('parent_id', 'select', array(
          'label'     => Mage::helper('cmspro')->__('Parent Category'),
          'required'  => true,
	  	  'values'    => $categoriesData,
          'name'      => 'parent_id',
      )); 
           
	  $fieldset->addField('identifier', 'text', array( 
          'label'     => Mage::helper('cmspro')->__('URL Key'),
          'name'      => 'identifier',
	  	  //'required'  => true,
      ));
      
      $fieldset->addField('order_cat', 'text', array(
          'label'     => Mage::helper('cmspro')->__('Order of Category'),
          'required'  => false,
          'name'      => 'order_cat',
      )); 
      
      $fieldset->addField('images', 'image', array(
          'label'     => Mage::helper('cmspro')->__('Images'),
          'required'  => false,
          'name'      => 'images',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('cmspro')->__('Is Active'),
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
          'note'    =>Mage::helper('cmspro')->__('Enable, save and reindex categories on first page after adding new category'),
      ));

      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('cmspro')->__('Description'),
          'title'     => Mage::helper('cmspro')->__('Description'),
          'style'     => 'width:600px; height:100px;',
          'wysiwyg'   => true,
          'required'  => true,
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
				// get array of selected store_id 
				$catStoreTableName = $collection->getTable('cmspro/category_store');
				$collection =  Mage::getModel('cmspro/category')->getCollection();
				$collection->getSelect()->join($catStoreTableName, $catStoreTableName.'.category_id = main_table.category_id AND main_table.category_id='.$catc->getCategoryId(), array($catStoreTableName.'.store_id'));
				
				if($collection->getData()){
					$arrStoreId = array();
					foreach($collection->getData() as $col){
						$arrStoreId[] = $col['store_id'];
					}					
					// set value for store view selected:
					//Mage::log($arrStoreId,Zend_Log::DEBUG,'cmspro.log');die;
					$form->getElement('store_id')->setValue($arrStoreId);
				} 
			 }
		 }
      }
      
      // set value for parent category selected:
      $selected = $JSONData['selected'];
      $form->getElement('parent_id')->setValue($selected);
      
      return parent::_prepareForm();
  }
  
  protected function _prepareParentCategoryJSON($categories){
  	  $conn = Mage::getModel('core/resource')->getConnection('core_write');
  	  $collection =  Mage::getModel('cmspro/category')->getCollection();
  	  //get parent id
  	  $id = $this->getRequest()->getParam('id');
  	  if($id)
  	  {
  	  	$parentId = Mage::getModel('cmspro/category')->load($id)->getParentId();
  	  }
  	  $categoriesData = array();
  	  $selected = array();
  	  $JSONData = array();
      foreach ($categories as $cat)
      {
      	  
      	
	      $readresult=$conn->query("SELECT store_id FROM ".$collection->getTable('cmspro/category_store')." WHERE category_id='".$cat['value']."'");
	  	  $storeId = array();
	      while ($row = $readresult->fetch() ){
			//Zend_Debug::dump($row);
			$storeId[] = $row['store_id'];
		  }
		  $value = json_encode( array('parent_id' => $cat['value'], 'store_id' => $storeId ) );
		  $categoriesData[] = array('value' => $value , 'label' => $cat['label'] );
		  
    	  //get parent category selected
      	  if( isset($parentId) && ($parentId) && ($parentId == $cat['value']) )
      	  {
      	  	$selected[] = $value;
      	  }
      }
      $JSONData['cat_data'] = $categoriesData;
      $JSONData['selected'] = $selected;
      return $JSONData;
  }
}