<?php
class MW_Cmspro_Block_Adminhtml_Tag_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmspro_tag_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }
    
	protected function _addColumnFilterToCollection($column)
    {
    	//if($column->getIndex()=='stores') {
        //    $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        //} else {
            parent::_addColumnFilterToCollection($column);
        //}
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('cmspro/tag_collection')
            //->addSummary(Mage::app()->getStore()->getId())
            ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'        => Mage::helper('cmspro')->__('Tag'),
            'index'         => 'name',
        ));

        $this->addColumn('popularity', array(
            'header'        => Mage::helper('cmspro')->__('Popularity'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'popularity',
            'type'          => 'number',
        ));

        $this->addColumn('status', array(
            'header'        => Mage::helper('cmspro')->__('Status'),
            'width'         => 90,
            'index'         => 'status',
            'type'          => 'options',
            'options'       => $this->helper('cmspro/data')->getStatusesArray(),
        ));

        return parent::_prepareColumns();
    }
    
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('tag')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('tag')->__('Are you sure?')
        ));

        $statuses = $this->helper('cmspro/data')->getStatusesOptionsArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('tag')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'status',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('tag')->__('Status'),
                    'values'   => $statuses
                )
             )
        ));

        return $this;
    }
    
	/*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ajaxGrid', array('_current' => true));
    }
    
    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('tag_id' => $row->getId()));
    }
        
}