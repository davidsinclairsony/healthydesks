<?php
class MW_Cmspro_Block_Adminhtml_Renderer_Categoryname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) {
    	if (empty($row['category_id'])) return '';
    	$model = Mage::getModel('cmspro/category');
    	$model_load = $model->load($row['category_id']);
    	$result = $model->level($model_load->getLevel()).$model_load->getName();
    	return $result;
    }
}