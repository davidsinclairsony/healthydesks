<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml assigned news grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Cmspro_Block_Adminhtml_Tag_Assigned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_currentTagModel;

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_currentTagModel = Mage::registry('cmspro_current_tag');
        $this->setId('cmspro_tag_assigned_news_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->_getTagId()) {
            $this->setDefaultFilter(array('in_news'=>1));
        }
    }

    /**
     * Tag ID getter
     *
     * @return int
     */
    protected function _getTagId()
    {
        return $this->_currentTagModel->getId();
    }

    /**
     * Store getter
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Add filter to grid columns
     *
     * @param mixed $column
     * @return Mage_Adminhtml_Block_Tag_Assigned_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in news flag
        if ($column->getId() == 'in_news') {
            $newsIds = $this->_getSelectedNews();
            if (empty($newsIds)) {
                $newsIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('news_id', array('in'=>$newsIds));
            } else {
                if($newsIds) {
                    $this->getCollection()->addFieldToFilter('news_id', array('nin'=>$newsIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve News Collection
     *
     * @return Mage_Adminhtml_Block_Tag_Assigned_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('cmspro/news')->getCollection();
            //->addAttributeToFilter('status', array(''))

        $this->setCollection($collection);

        parent::_prepareCollection();
        //$this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    /**
     * Prepeare columns for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
    	$this->setTemplate('cmspro/news/grid.phtml');
        $this->addColumn('in_news', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'field_name'        => 'in_news',
            'values'            => $this->_getSelectedNews(),
            'align'             => 'center',
            'index'             => 'news_id'
        ));

        $this->addColumn('news_id',
            array(
                'header'=> Mage::helper('cmspro')->__('ID'),
                'align'     =>'right',
                'width' => '50px',
                'sortable'  => true,
                'type'  => 'number',
                'index' => 'news_id',
        ));
        $this->addColumn('title',
            array(
                'header'=> Mage::helper('cmspro')->__('Title'),
                'index' => 'title',
        ));

        $this->addColumn('images', array(
          'header'    => Mage::helper('cmspro')->__('Images'),
          'align'     =>'left',
	  	  'width'     =>'80px',
          'index'     => 'images',
     	 ));
      
     	$this->addColumn('allowcomment', array(
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


        return parent::_prepareColumns();
    }

    /**
     * Retrieve related news
     *
     * @return array
     */
    protected function _getSelectedNews()
    {
        $news = $this->getRequest()->getPost('assigned_news', null);
        if (!is_array($news)) {
            $news = $this->getRelatedNews();
        }
        //Zend_Debug::dump($news);die;
        return $news;
    }

    /**
     * Retrieve Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/assignedGridOnly', array('_current' => true));
    }

    /**
     * Retrieve related news
     *
     * @return array
     */
    public function getRelatedNews()
    {
        return $this->_currentTagModel
            ->setStatusFilter(MW_Cmspro_Model_Tag::STATUS_ENABLED)
            ->getRelatedNewIds();
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
