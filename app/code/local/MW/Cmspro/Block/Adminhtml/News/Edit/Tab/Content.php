<?php

class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Content 
	extends Mage_Adminhtml_Block_Widget_Form  
		implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected $_tagOptionsArray = array();
	protected $_tagSelectedArray = array();
	
   /**
     * Load Wysiwyg on demand and Prepare layout
     */
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $version = Mage::getVersion();
        if(version_compare($version, '1.4.0.1', '>=')===true){
	        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
	            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
	        }
        }
    }
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $form->setHtmlIdPrefix('news_');
      $this->setForm($form);
      $fieldset = $form->addFieldset('content_fieldset', array('legend'=>Mage::helper('cmspro')->__('Content'),'class'=>'fieldset-wide'));
	  $wysiwygConfig = "";
	  //echo Mage::getVersion();exit;
	  $version = Mage::getVersion();
	  if(version_compare($version, '1.4.0.1', '>=')===true){
	      $wysiwygConfig = Mage::getSingleton('cmspro/wysiwyg_config')->getConfig(
	            array('tab_id' => $this->getTabId())
	        );
	  }
	  
   	  try {
            $config = Mage::getSingleton('cmspro/wysiwyg_config')->getConfig();
            $config->setData(Mage::helper('cmspro/data')->recursiveReplace(
                            '/cmspro_admin/', '/' . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/', $config->getData()
                    )
            );
      } catch (Exception $ex) {
            $config = null;
      }
      /*
       $toolbar_config = array(
       		'plugins' => 'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks,codesyntax',
            'theme_advanced_buttons1' => 'bold,italic,separator,link,unlink,separator,undo,redo,codeformat',
            'theme_advanced_buttons2' => '',
            'theme_advanced_buttons3' => '',
            'theme_advanced_buttons4' => '',
        );

      
        $config->setData(array_merge($config->getData(), $toolbar_config));
        */
        
      
      //$wysiwygConfig-> 
     $summaryField = $fieldset->addField('summary', 'editor', array(
          'label'     => Mage::helper('cmspro')->__('Summary'),
          'class'     => 'required-entry',
     	  'style'     => 'width:640px; height:50px;',
          'required'  => true,
          'name'      => 'summary',
      	  'config'    => $config
      ));
      
  	 
      
     $contentField = $fieldset->addField('content', 'editor', array(
            'label'     => Mage::helper('cmspro')->__('Content'),
            'name'      => 'content',
            'style'     => 'height:36em;',
            'required'  => true,
            'config'    => $config
        ));
        
      //tag
      $fieldset = $form->addFieldset('tag_form', array(
      	  'legend'=>Mage::helper('cmspro')->__('Additional Information')
      ));
      $this->tagToOptionsArray();
      $fieldset->addField('tags', 'multiselect', array(
          'label'     => Mage::helper('cmspro')->__('News Tags'),
          'name'      => 'tags',
      	  'values'	  => $this->_tagOptionsArray,
      ));
      
      $fieldset->addField('author', 'text', array( 
	          'label'     => Mage::helper('cmspro')->__('Author'),
	          'name'      => 'author',
      	  ));
        
       // Setting custom renderer for content field to remove label column
       $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                    ->setTemplate('cmspro/news/edit/form/renderer/content.phtml');
       $contentField->setRenderer($renderer);
       $summaryField->setRenderer($renderer);
      if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('cmspronews_data') ) {
          $form->setValues(Mage::registry('cmspronews_data')->getData());
      }
      //Zend_Debug::dump($this->_tagSelectedArray);die;
      $form->getElement('tags')->setValue($this->_tagSelectedArray);
      Mage::dispatchEvent('adminhtml_news_edit_tab_content_prepare_form', array('form' => $form));
      return parent::_prepareForm();
  }
  
    public function getTabLabel()
    {
        return Mage::helper('cmspro')->__('Content');
    }

    public function getTabTitle()
    {
        return Mage::helper('cmspro')->__('Content');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cmspro/adminhtml/news/' . $action);
    }
    
  	public function tagToOptionsArray()
  	{
  		$collection = $this->_getCollection();
  		if( $collection AND ( (!$this->_tagOptionsArray) OR (!$this->_tagSelectedArray) ) )
  		{
  			foreach ($collection as $tag)
  			{
  				$tagName = $tag->getName();
  				$this->_tagSelectedArray[] = $tagName;
  				$option['value'] = $tagName;
  				$option['label'] = $tagName;
  				$this->_tagOptionsArray[] = $option;
  			}
  		} 
  	}
  	    
  	protected function _getCollection()
  	{
        if( $this->getNewsId() ) {
            $model = Mage::getModel('cmspro/tag');
            return $collection = $model->getResourceCollection()
                //->addStatusFilter(MW_Cmspro_Model_Tag::STATUS_ENABLED)
                ->addNewsFilter($this->getNewsId())
                ->load();
        }
        return false;
  	} 

	public function getNewsId()
    {
        return ($this->getRequest()->getParam('id')) ? $this->getRequest()->getParam('id') : false;
    }  	
}