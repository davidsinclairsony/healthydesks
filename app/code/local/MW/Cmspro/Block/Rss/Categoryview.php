<?php
class MW_Cmspro_Block_Rss_Categoryview extends Mage_Rss_Block_Abstract
{
 	protected function _toHtml()
    {
        $rssObj = Mage::getModel('rss/rss');

        $data = array('title' => 'News',
		              'description' => 'News',
		              'link'        => $this->getUrl('cmspro/rss'),
		              'charset'     => 'UTF-8',
		              'language'    => Mage::getStoreConfig('general/locale/code')
		            );

        $rssObj->_addHeader($data);

	    // Get news order by created_time by store view
	    $collection = Mage::getModel('cmspro/news')->getCollection()->addStatusFilter(1)->setOrder('created_time', 'desc');
	    
	    //add storeview filter
	    $storeView = Mage::app()->getStore()->getId();
	    $collection->getSelect()->join(array('ns'=>$collection->getTable('news_store')),
	    							   'main_table.news_id = ns.news_id') ;
	    $collection->addFieldToFilter('ns.store_id',array('fdin'=>array($storeView,0)));
	    
		// add restriction filter
		$session = Mage::getSingleton('customer/session');
		$cid = $session->getId();
		$cig = $session->getCustomerGroupId();
		$collection->addRestrictionFilter($cig,$cid);
		
	    if ($this->getRequest()->get('cid')) {
        	$category_id = $this->getRequest()->get('cid');

        	$cat = Mage::getModel('cmspro/category')->load($category_id);
        	$table_category = $collection->getTable('category');

		    $collection->getSelect()
					->join(
						array('category'=>$collection->getTable('news_category')),
						"category.news_id = main_table.news_id 
							AND 
							 category.category_id in (
							 	SELECT ".$table_category.".category_id FROM ".$table_category." 
							 		WHERE ".$table_category.".root_path LIKE '".$cat->getRootPath()."%'
									AND ".$table_category.".status = 1
							 )
						",array('category.category_id'))->group('main_table.news_id');
	    }
			    
	    $collection->setPageSize(50);
	    $collection->setCurPage(1);
	    

        if ($collection->getSize()>0) {
        	$args = array('rssObj' => $rssObj);
            foreach ($collection as $item) {
            	$args['news'] = $item;
                $this->addNewItemXmlCallback($args);
            }
        } else {
             $data = array('title' => Mage::helper('cmspro')->__('Cannot retrieve the news'),
                    'description' => Mage::helper('cmspro')->__('Cannot retrieve the news'),
                    'link'        => Mage::getUrl(),
                    'charset'     => 'UTF-8',
                );
             $rssObj->_addHeader($data);
        }

        return $rssObj->createRssXml();
    }
    
	public function addNewItemXmlCallback($args)
    {
        $news = $args['news'];

		$url = Mage::getBaseUrl().Mage::getModel('core/url_rewrite')->load($news->getUrlRewriteId())->getRequestPath();
        $description = '<table><tr>'
                     . '<td><a href="'.$url.'"><img src="'
                     . $this->helper('cmspro/image')->init($news->getImages())->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $news->getSummary();

        $description .= '</td></tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $news->getTitle(),
                'link'          => $url,
                'description'   => $description,
        		'lastUpdate'    => strtotime($news->getUpdateTime()),
            );

        $rssObj->_addEntry($data);
    }
    
}