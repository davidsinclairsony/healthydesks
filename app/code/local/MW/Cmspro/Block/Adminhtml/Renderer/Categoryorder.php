<?php
class MW_Cmspro_Block_Adminhtml_Renderer_Categoryorder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
	public function render(Varien_Object $row) {
		
		$html = "";
		$model = Mage::getModel('cmspro/category')->load($row['category_id']);
		$html = '<input type="text" class="order_cat" title="'.$model->getCategoryId().'" style="width:60px;" id="order_cat'.$model->getOrderCat().'" value="'.$model->getOrderCat().'">';
        
		return $html;
    }
}