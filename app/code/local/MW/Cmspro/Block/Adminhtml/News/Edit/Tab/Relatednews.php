<?php

class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Relatednews extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('related_news_grid');
        $this->setDefaultSort('news_id');
        //$this->setDefaultDir('ASC');
        $this->setUseAjax(true);
		$this->setDefaultFilter(array('in_news' => 1));
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
     */
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in news flag
        if ($column->getId() == 'in_news') 
        {
            $newsIds = $this->_getSelectedNews();
            if (empty($newsIds)) {
                $newsIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('news_id', array('in' => $newsIds));
            }else{
            	$this->getCollection()->addFieldToFilter('news_id', array('nin' => $newsIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
    	$newsId = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'):array(0);
		$collection = Mage::getModel('cmspro/news')->getCollection()
		->addFieldToFilter('news_id', array('nin' => $newsId))->setOrder('created_time','DESC');
		$collection->addStatusFilter(1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
	
        return false;// $this->_getProduct()->getReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
	protected function _prepareColumns()
  	{
  	  $this->setTemplate('cmspro/news/grid.phtml');
  	  
  	  $this->addColumn('in_news', array(
           'header_css_class'  => 'a-center',
           'type'              => 'checkbox',
           'name'              => 'in_news',
           'values'            => $this->_getSelectedNews(),
           'align'             => 'center',
           'index'             => 'news_id'
      ));
            
      $this->addColumn('news_id_related', array(
          'header'    => Mage::helper('cmspro')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'news_id',
      ));
      
	  $this->addColumn('images_related', array(
          'header'    => Mage::helper('cmspro')->__('Images'),
          'align'     =>'left',
	  	  'width'     =>'80px',
          'index'     => 'images',
      ));
      
      $this->addColumn('title_filter_related', array(
          'header'    => Mage::helper('cmspro')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	$this->addColumn('news_allowcomment_related', array(
          'header'    => Mage::helper('cmspro')->__('Comment'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'allowcomment',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
      $this->addColumn('news_status_related', array(
          'header'    => Mage::helper('cmspro')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      $this->addColumn('new_created_time_related', array(
          'header'    => Mage::helper('cmspro')->__('Created Date'),
          'align'     =>'left',
          'index'     => 'created_time',
      ));

      $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'width'             => '0px',
            'editable'          => true,
            //'edit_only'         => !$this->_getProduct()->getId()
        ));
	  
      return parent::_prepareColumns();
	}
	
	  public function getNewsThumbnailSize(){
	  	$size = Mage::getStoreConfig('mw_cmspro/info/backend_thumbnail_size');
			$tmp = explode('-',$size);
			if(sizeof($tmp)==2){
				return array('width'=>is_numeric($tmp[0])?$tmp[0]:60,'height'=>is_numeric($tmp[1])?$tmp[1]:60);
			}
			return array('width'=>60,'height'=>60);
	  }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/relatednewsgrid', array('_current' => true));
    }

    /**
     * Retrieve selected related newss
     *
     * @return array
     */
    protected function _getSelectedNews()
    {
        $news = $this->getNewsRelated();
		
        if (!is_array($news)) {
            $news = array_keys($this->getSelectedRelatedNews());
        }
        return $news;
    }

    /**
     * Retrieve related news
     *
     * @return array
     */
    public function getSelectedRelatedNews()
    {
        $news = array();
		
		
		if(!Mage::registry("cmspro_related_news")){
		
			$colRelated = $this->getRelatedNews();
			//Zend_Debug::dump($colRelated);die;
			Mage::register("cmspro_related_news", $colRelated);
		}
		
		$id = $this->getRequest()->getParam('id');
		//if(count(Mage::registry("news_related_data"))> 0)
        foreach (Mage::registry("cmspro_related_news") as $newsData) {
            $news[$newsData->getRelatedNewsId()] = array('position' => $newsData->getPosition());
        }
        
		//Zend_Debug::dump($news);die;
        return $news;
    }
	
	public function getRelatedNews(){
	$id = $this->getRequest()->getParam('id');
		/*$collection = Mage::getModel('cmspro/newsnews')
					->getCollection()
					->addFieldToFilter('news_id', $id)
					->load()
					;	*/
		try{
		$collection = Mage::getModel('cmspro/newsnews')
					->getCollection();
		$collection->addFieldToFilter('news_id',$id);
		//Zend_Debug::dump($collection);die;
		}catch(Exception $e)
		{
			echo $e->getMessage();die;
		}	
		return $collection;
	}
	
	
}