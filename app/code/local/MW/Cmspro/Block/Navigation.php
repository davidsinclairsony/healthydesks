<?php
class MW_Cmspro_Block_Navigation extends Mage_Catalog_Block_Navigation
{
 
	public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '') 
	{
	 
		$categories = (Mage::getModel('cmspro/category')->getRootCategory());
		$categories->addFieldToFilter('status',"1");

		$categories->getSelect()->join(
			array('store'=>$categories->getTable('category_store')),
			'main_table.category_id = store.category_id',
			array('store.store_id')
		)->where('store.store_id in (?)',array('0',Mage::app()->getStore()->getId())); 

		// if route is active
		$active = ($this->getRequest()->getRouteName()=='cmspro' ? 'active' : '');
	 
		// Get navigation menu html
		$html = parent::renderCategoriesMenuHtml($level, $outermostItemClass, $childrenWrapClass);
	 
		$current_cat_id = (isset($_SESSION['cmspro_current_cat'])) ? $_SESSION['cmspro_current_cat']:"";
		$current_cat_id = (($this->getRequest()->getParam('id')) && ($this->getRequest()->getModuleName()=="cmspro") && ($this->getRequest()->getControllerName()=="category")) ? $this->getRequest()->getParam('id') : $current_cat_id;
		//$news_current = ($this->getRequest()->getModuleName()=="cmspro") ? true:false;
        // News Category menu
        $a = (Mage::getStoreConfig('mw_cmspro/left_menu_category/enable')) ? Mage::getStoreConfig('mw_cmspro/left_menu_category/enable'):"";
        if($a=="1"){  // if module is active
			if(Mage::getStoreConfig('mw_cmspro/left_menu_category/show_root_category')=="1"){
				$root_menu = Mage::getModel('cmspro/category')->load(1);
				
			    // Adding new menu item. You can also add few items or child elements there
			    $html .=  "
			    <li class='level0 nav-20 level-top parent last $active' onmouseout='toggleMenu(this,0)' onmouseover='toggleMenu(this,1)'>
			    	<a class='$outermostItemClass' href='" . Mage::getBaseUrl().$root_menu->_getUrlRewrite() . "'>
			    	<span>" . $root_menu->getName() . "</span></a>";
			  	$html .= "<ul class='level0'>";
						foreach($categories as $cat){
							$html .=  Mage::getModel('cmspro/category')->drawItem($cat);
						}
			  	$html .= "
			  			  </ul>
		     	</li>";
			}
        }
		return $html;
	}
}