<?php

class MW_Cmspro_Block_Rss_Listnew extends Mage_Rss_Block_List
{
    public function CategoriesRssFeed()
    {
		parent::CategoriesRssFeed();
        $_main_categories = Mage::getModel('cmspro/category')->getRootCategory();
		foreach ($_main_categories as $_main_category){
			$this->addRssFeed('cmspro/rss/category', $_main_category->getName(),array('cid'=>$_main_category->getId()));
			
		}

    }
}
