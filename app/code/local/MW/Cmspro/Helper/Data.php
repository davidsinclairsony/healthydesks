<?php

class MW_Cmspro_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getProEdition()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('Mage_Ogone',$modules)) 
		{
			return true;
		}
		return false;
	}
	
	public function recursiveReplace($search, $replace, $subject)
    {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }
	
    public function closetags($html){
        #put all opened tags into an array
        preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
        $openedtags = $result[1];
    
        #put all closed tags into an array
        preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
        $closedtags = $result[1];
        $len_opened = count ( $openedtags );
        # all tags are closed
        if( count ( $closedtags ) == $len_opened )
        {
            return $html;
        }
        $openedtags = array_reverse ( $openedtags );
        # close tags
        for( $i = 0; $i < $len_opened; $i++ )
        {
            if ( !in_array ( $openedtags[$i], $closedtags ) )
            {
                $html .= "</" . $openedtags[$i] . ">";
            }
            else
            {
                unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
            }
        }
        return $html;
    }
	/**
     * Return template for setTemplate function in layout to display latest news
     *
     * @return string
     */
    public function displayFeatureRight(){
    	$position = Mage::getStoreConfig('mw_cmspro/news/display_latest_news');
    	if($position == 0)
    	{
    		return 'cmspro/block/latest.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display latest news
     *
     * @return string
     */
    public function displayFeatureLeft(){
    	$position = Mage::getStoreConfig('mw_cmspro/news/display_latest_news');
    	if($position == 1)
    	{
    		return 'cmspro/block/latest.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display latest news
     *
     * @return string
     */
    public function displayRelatedNewsRight(){
    	$position = Mage::getStoreConfig('mw_cmspro/relatednews/display_related_news');
    	if($position == 0)
    	{
    		return 'cmspro/view/relatednews.phtml';
    	}
    }
	/**
     * Return template for setTemplate function in layout to display latest news
     *
     * @return string
     */
    public function displayRelatedNewsLeft(){
    	$position = Mage::getStoreConfig('mw_cmspro/relatednews/display_related_news');
    	if($position == 1)
    	{
    		return 'cmspro/view/relatednews.phtml';
    	}
    }

    /**
     * Return template for setTemplate function in layout to set root (1column,2column or 3column)
     *
     * @return string
     */
    public function chooseColumnLayout()
    {
    	$columnNumber = Mage::getStoreConfig('mw_cmspro/design/layout'); 
    	switch($columnNumber){
    		case 'empty':
    			return 'page/empty.phtml';
    			break;
    		case 'one_column':
    			return 'page/1column.phtml';
    			break;
    		case 'two_columns_left':
    			return 'page/2columns-left.phtml';
    			break;
    		case 'two_columns_right':
    			return 'page/2columns-right.phtml';
    			break;
    		case 'three_columns':
    			return 'page/3columns.phtml';
    			break;  
    		default: 
    			return 'page/3columns.phtml';
    			break;  			    			    			
    	}
    }
    
	public function getStatusesArray()
    {
        return array(
            MW_Cmspro_Model_Tag::STATUS_DISABLED => Mage::helper('cmspro')->__('Disabled'),
            MW_Cmspro_Model_Tag::STATUS_ENABLED => Mage::helper('cmspro')->__('Enable')
        );
    }
    
    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('cmspro')->__('Disabled'),
                'value' => MW_Cmspro_Model_Tag::STATUS_DISABLED,
            ),
            array(
                'label' => Mage::helper('cmspro')->__('Enable'),
                'value' => MW_Cmspro_Model_Tag::STATUS_ENABLED,
            )
        );
    }
    
    public function getTitleIndex(){
    	$store_id = Mage::app()->getStore()->getId();
    	if(Mage::getStoreConfig('mw_cmspro/news/title_index'))
    		return Mage::getStoreConfig('mw_cmspro/news/title_index');
    	return Mage::getStoreConfig('design/head/default_title');
    }
    
    public function getMetaDescriptionIndex(){
    	$store_id = Mage::app()->getStore()->getId();
    	if(Mage::getStoreConfig('mw_cmspro/news/meta_description_index',$store_id))
    		return Mage::getStoreConfig('mw_cmspro/news/meta_description_index',$store_id);
    	return Mage::getStoreConfig('design/head/default_description');
    }
    
    public function getMetaKeywordIndex(){
    	$store_id = Mage::app()->getStore()->getId();
    	if(Mage::getStoreConfig('mw_cmspro/news/meta_keyword_index',$store_id))
    		return Mage::getStoreConfig('mw_cmspro/news/meta_keyword_index',$store_id);
    	return Mage::getStoreConfig('design/head/default_keywords');
    }
    
	public function renderRootMenuNews(){
    	$categories = (Mage::getModel('cmspro/category')->getRootCategory());
		$categories->addFieldToFilter('status',"1");

		$categories->getSelect()->join(
			array('store'=>$categories->getTable('category_store')),
			'main_table.category_id = store.category_id',
			array('store.store_id')
		)->where('store.store_id in (?)',array('0',Mage::app()->getStore()->getId())); 
    	
        // News Category menu
        $a = (Mage::getStoreConfig('mw_cmspro/left_menu_category/enable')) ? Mage::getStoreConfig('mw_cmspro/left_menu_category/enable'):"";
        if($a=="1"){  // if module is active
			if(Mage::getStoreConfig('mw_cmspro/left_menu_category/show_root_category')=="1"){
				$html = "";
				$root_menu = Mage::getModel('cmspro/category')->load(1);
				
			    // Adding new menu item. You can also add few items or child elements there
			    $html .=  "
			    <li class='level0 nav-20 level-top parent last 1' onmouseout='toggleMenu(this,0)' onmouseover='toggleMenu(this,1)'>
			    	<a class='level-top' href='" . Mage::getBaseUrl().$root_menu->_getUrlRewrite() . "'>
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
	
	const MYNAME = "MW_Cmspro";
	function disableConfig()
	{		
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			Mage::getConfig()->reinit();
	}
	
	
}