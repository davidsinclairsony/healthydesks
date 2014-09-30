<?php

class MW_Cmspro_Block_Adminhtml_News_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('newsGrid');
      $this->setDefaultSort('news_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      //$this->setTemplate('cmspro/news/grid.phtml');
      $this->setEmptyText(Mage::helper('cmspro')->__('There is no news in this category'));
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('cmspro/news')->getCollection()->setOrder('created_time','DESC');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	 // $this->setTemplate('cmspro/news/grid.phtml');
      $this->addColumn('news_id', array(
          'header'    => Mage::helper('cmspro')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'news_id',
      ));
      
	  $this->addColumn('images', array(
          'header'    => Mage::helper('cmspro')->__('Images'),
          'align'     =>'left',
	  	  'width'     =>'80px',
          'index'     => 'images',
	      'renderer'  => 'cmspro/adminhtml_renderer_newsimages',
      ));
      
      $this->addColumn('title', array(
          'header'    => Mage::helper('cmspro')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      $this->addColumn('feature', array(
          'header'    => Mage::helper('cmspro')->__('Featured'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'feature',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
      
	$this->addColumn('allowcomment', array(
          'header'    => Mage::helper('cmspro')->__('Comment'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'allowcomment',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      $this->addColumn('status', array(
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
      $this->addColumn('created_time', array(
          'header'    => Mage::helper('cmspro')->__('Created Date'),
          'align'     =>'left',
          'index'     => 'created_time',
      ));        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('cmspro')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('cmspro')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('cmspro')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('cmspro')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('news_id');
        $this->getMassactionBlock()->setFormFieldName('news');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('cmspro')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('cmspro')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('cmspro/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('cmspro')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('cmspro')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  public function getNewsThumbnailSize(){
  	$size = Mage::getStoreConfig('mw_cmspro/info/backend_thumbnail_size');
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2){
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:60,'height'=>is_numeric($tmp[1])?$tmp[1]:60);
		}
		return array('width'=>60,'height'=>60);
  }
}