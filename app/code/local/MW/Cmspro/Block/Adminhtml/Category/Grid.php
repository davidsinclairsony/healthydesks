<?php

class MW_Cmspro_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('categoryGrid');
      $this->setDefaultSort('category_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
     // $this->setTemplate('cmspro/category/grid.phtml');
      $this->setEmptyText(Mage::helper('cmspro')->__('There is no news in this category'));
  }
	public function getMultipleRows($item)
    {
        return array();
    }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('cmspro/category')->getCollection()
			      ->addFieldToFilter('category_id', array('neq' => 1))
			      ->setOrder('order_cat','ASC')
			      ->setOrder('level','ASC');
	  
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {    	
      $this->addColumn('category_id', array(
          'header'    => Mage::helper('cmspro')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'category_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('cmspro')->__('Category Name'),
          'align'     =>'left',
          'index'     => 'name',
      	  'renderer'  => 'cmspro/adminhtml_renderer_categoryname',
      ));
      
      $this->addColumn('level', array(
          'header'    => Mage::helper('cmspro')->__('Level'),
          'align'     =>'left',
          'index'     => 'level',
          'width'     => '80px',
      ));

      $this->addColumn('created_time', array(
          'header'    => Mage::helper('cmspro')->__('Created Time'),
          'align'     =>'center',
      	  'width'     => '160px',
          'index'     => 'created_time',
      ));
      
      $this->addColumn('order_cat', array(
          'header'    => Mage::helper('cmspro')->__('Order'),
          'align'     =>'center',
          'index'     => 'order_cat',
          'width'     => '100px',	
          'renderer'  => 'cmspro/adminhtml_renderer_categoryorder',
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
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category');

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