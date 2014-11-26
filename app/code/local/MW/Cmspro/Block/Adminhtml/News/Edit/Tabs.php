<?php

class MW_Cmspro_Block_Adminhtml_News_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('news_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cmspro')->__('News Information'));
  }

	protected function _prepareLayout()
    {
        $product = $this->getNews();

        
        return parent::_prepareLayout();
    }

	/**
     * Retrive news object from object if not from registry
     *
     * @return MW_Cmspro_Model_News
     */
    public function getNews()
    {
        if (!($this->getData('news') instanceof MW_Cmspro_Model_News)) {
            $this->setData('news', Mage::registry('news'));
        }
        return $this->getData('news');
    }

  
  protected function _beforeToHtml()
  {
   $product = $this->getNews();

    $this->addTab('general_section', array(
          'label'     => Mage::helper('cmspro')->__('General'),
          'title'     => Mage::helper('cmspro')->__('General'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_general')->toHtml(),
      ));
  	$this->addTab('content_section', array(
		  'label'     => Mage::helper('cmspro')->__('Content/Tags'),
		  'title'     => Mage::helper('cmspro')->__('Content/Tags'),
		  'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_content')->toHtml(),
	  ));
	$this->addTab('design_section', array(
		  'label'     => Mage::helper('cmspro')->__('Design'),
		  'title'     => Mage::helper('cmspro')->__('Design'),
		  'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_design')->toHtml(),
	  ));
	  
  	$this->addTab('related', array(
		  'label'     => Mage::helper('cmspro')->__('Related Products'),
		  'url'       => $this->getUrl('*/*/related', array('_current' => true)),
		  'class'     => 'ajax',
	  ));
  	$this->addTab('related_news', array(
		  'label'     => Mage::helper('cmspro')->__('Related News'),
		  'url'       => $this->getUrl('*/*/relatednews', array('_current' => true)),
		  'class'     => 'ajax',
	  ));
	$this->addTab('restric', array(
          'label'     => Mage::helper('cmspro')->__('User Restriction'),
          'title'     => Mage::helper('cmspro')->__('User Restriction'),
          'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_restriction')->toHtml(),
      ));	
 	$this->addTab('meta_section', array(
		  'label'     => Mage::helper('cmspro')->__('Meta Information'),
		  'title'     => Mage::helper('cmspro')->__('Meta Information'),
		  'content'   => $this->getLayout()->createBlock('cmspro/adminhtml_news_edit_tab_meta')->toHtml(),
	  ));
      return parent::_beforeToHtml();
  }
  
   public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('cmspro/news')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('cmspro/news')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }
	
    /**
     * Translate html content
     * 
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}