<?php
class MW_Cmspro_Helper_Category extends Mage_Core_Helper_Abstract
{
	public function getSitemapCategoryUrl()
    {
        return $this->_getUrl('cmspro/sitemap/category');
    }

    public function getSitemapNewsUrl()
    {
        return $this->_getUrl('cmspro/sitemap/news');
    }
	/**
     * Return template for setTemplate function in layout to display category menu
     *
     * @return string
     */
    public function displayMenuAccordionRight(){
    	$position = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_position");
		$style = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_style");
    	if( ($position==0)&& ($style==0) )
    	{
    		return 'cmspro/category/accordion_right.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display category menu
     *
     * @return string
     */
    public function displayMenuDropdownRight(){
    	$position = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_position");
		$style = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_style");
    	if( ($position==0)&& ($style==1) )
    	{
    		return 'cmspro/category/dropdown_right.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display category menu
     *
     * @return string
     */
    public function displayMenuAccordionLeft(){
    	$position = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_position");
		$style = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_style");
    	if( ($position==1)&& ($style==0) )
    	{
    		return 'cmspro/category/accordion_left.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display category menu
     *
     * @return string
     */
    public function displayMenuDropdownLeft(){
    	$position = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_position");
		$style = Mage::getStoreConfig("mw_cmspro/left_menu_category/side_menu_style");
    	if( ($position==1)&& ($style==1) )
    	{
    		return 'cmspro/category/dropdown_left.phtml';
    	}
    }
}
?>