<?php

class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_General 
	extends Mage_Adminhtml_Block_Widget_Form 
		implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
  protected function _prepareForm()
  {
  	  //var_dump(Mage::registry('cmspronews_data')); exit;
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('general_form', array('legend'=>Mage::helper('cmspro')->__('General information')));
      $fieldset_new = $form->addFieldset('general_form_backup', array('legend'=>Mage::helper('cmspro')->__('Revisions')));
      
     $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('cmspro')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

	  $fieldset->addField('identifier', 'text', array( 
          'label'     => Mage::helper('cmspro')->__('URL Key'),
          'name'      => 'identifier',
	  	  //'required'  => true,
      ));
     $categories = array();
     $categories = Mage::getModel('cmspro/category')->getTreeCategory();
	  
	  $fieldset->addField('category_id', 'multiselect', array(
          'label'     => Mage::helper('cmspro')->__('Categories'),
          'required'  => true,
	  	  'values'    => $categories,
	  	  'style'	  => 'height:150px',
          'name'      => 'news_category[]',
	      'note'      => Mage::helper('cmspro')->__('Add categories under CMS Pro - Manage Categories.'),
      )); 
      
	 /* if (!Mage::app()->isSingleStoreMode()) {
	            $fieldset->addField('store_id', 'multiselect', array(
	                'name'      => 'news_stores[]',
	                'label'     => Mage::helper('cms')->__('Store View'),
	                'title'     => Mage::helper('cms')->__('Store View'),
	                'required'  => true,
	    	        'style'	    => 'height:120px',
	                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
	            ));
	   }*/
	 	$fieldset->addField('images', 'image', array(
	          'label'     => Mage::helper('cmspro')->__('Image'),
	          'required'  => false,
	          'name'      => 'images',
	  	));
	  	
	  	$fieldset->addField('allow_show_image', 'select', array( 
          'label'     => Mage::helper('cmspro')->__('Show image/summary in news detail'),
          'name'      => 'allow_show_image',
	      'values'    => array(
		              array(
		                  'value'     => 1,
		                  'label'     => Mage::helper('cmspro')->__('Yes'),
		              ),
		
		              array(
		                  'value'     => 0,
		                  'label'     => Mage::helper('cmspro')->__('No'),
		              ),
		          ),
        ));
        
		$fieldset->addField('feature', 'select', array( 
          'label'     => Mage::helper('cmspro')->__('Featured News'),
          'name'      => 'feature',
	      'values'    => array(
		              array(
		                  'value'     => 1,
		                  'label'     => Mage::helper('cmspro')->__('Yes'),
		              ),
		
		              array(
		                  'value'     => 0,
		                  'label'     => Mage::helper('cmspro')->__('No'),
		              ),
		          ),
		   'note'  => Mage::helper('cmspro')->__('Will display on 1<sup>st</sup> page of CMS Pro'),
        ));
        
        $fieldset->addField('allowcomment', 'select', array(
          'label'     => Mage::helper('cmspro')->__('Enable Comments'),
          'name'      => 'allowcomment',
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
	      ));

	      if($this->getRequest()->getParam('id')){
	      	  
	      	  $news_backup_data = array();
	      	  $news_backup_data[0] = Mage::helper('cmspro')->__('Please select news backup');
	      	  $collection_backups = Mage::getModel('cmspro/newsbackup')->getCollection()
	      	  		->addFieldToFilter('news_id_parent',$this->getRequest()->getParam('id'))
	      	  		->setOrder('update_time','DESC');
	      	  
	      	  $i = 0;
	      	  foreach ($collection_backups as $collection_backup) {
	      	  	
	      	  	$time_backup = Mage::helper('core')->formatDate($collection_backup->getUpdateTime(),Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)." ".Mage::helper('core')->formatTime($collection_backup->getUpdateTime());
	      	  	
	      	  	if($i == 0) $time_backup = $time_backup.' (Newest)';
	      	  	$news_backup_data[$collection_backup->getNewsId()]= Mage::helper('cmspro')->__('Revision ').$time_backup;
	      	  	$i++;
	      	  
	      	  }
	      	  
		      $backupField = $fieldset_new->addField('news_backup', 'select', array(
		          'label'     => Mage::helper('cmspro')->__('Load previous revision'),
		          'name'      => 'news_backup',
		          'values'    => $news_backup_data,
		          'note'      => Mage::helper('cmspro')->__('Display previously saved version of article/blog'),
		      ));
		      
		      $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                            ->setTemplate('cmspro/news/edit/form/renderer/backup.phtml');
       		  $backupField->setRenderer($renderer);
       
	      }
	      
	          
      if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('cmspronews_data') ) {
          $form->setValues(Mage::registry('cmspronews_data')->getData());
          $news = Mage::registry('cmspronews_data');

      	  if($news->getNewsId()){
        	$arrStoreId = array();
			$arrCategoryId = array();
				  	
         	$collection_categories =  Mage::getModel('cmspro/news')->getCollection();
			$table_cat = $collection_categories->getTable('news_category'); 

			$collection_categories->getSelect()
						->join(array('table_cat'=>$table_cat), 'table_cat.news_id = main_table.news_id', array('table_cat.category_id'))
						->where('main_table.news_id=?',$news->getNewsId());
						

			if($collection_categories->getData()){
				foreach($collection_categories->getData() as $col){
					if(isset($col['category_id'])) { $arrCategoryId[] = $col['category_id'];}
				}
			}

			$form->getElement('category_id')->setValue($arrCategoryId);
		 }
      }
      return parent::_prepareForm();
  }
    public function getTabLabel(){
    	return Mage::helper('cmspro')->__('General');
    }
    public function getTabTitle(){
    	return Mage::helper('cmspro')->__('General');
    }
    public function canShowTab(){
    	return true;
    }
    public function isHidden(){
    	return false;
    }
}