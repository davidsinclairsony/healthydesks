<?php

class MW_Cmspro_Block_Adminhtml_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  { 
      parent::__construct();
      $this->setId('commentGrid');
      $this->setDefaultSort('comment_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
	
      $collection = Mage::getModel('cmspro/comment')->getCollection();
      $this->setCollection($collection);
      parent::_prepareCollection();
      return  $collection;
  }

  protected function _prepareColumns()
  {
      $this->addColumn('comment_id', array(
          'header'    => Mage::helper('cmspro')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'comment_id',
      ));

	  
      $this->addColumn('fullname', array(
			'header'    => Mage::helper('cmspro')->__('Author name'),
			'index'     => 'fullname',
      ));

      $this->addColumn('content', array(
          'header'    => Mage::helper('cmspro')->__('Content'),
          'align'     =>'left',
          'index'     => 'content',
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
        $this->setMassactionIdField('comment_id');
        $this->getMassactionBlock()->setFormFieldName('comment');

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

}