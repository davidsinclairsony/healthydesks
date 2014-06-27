<?php
class MW_Cmspro_Block_Adminhtml_Renderer_Newsimages extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) {
    	if (empty($row['images'])) return '';
    	return '<img src="'.Mage::helper('cmspro/image')->init($row['images'])->resize(60, 60). '" />';
    }
}