<?php
class MW_Cmspro_TagController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->renderLayout();
	}	
	
	/**
	 * Tagged products controller
	 *
	 * @category   Mage
	 * @package    Mage_Tag
	 * @author     Magento Core Team <core@magentocommerce.com>
	 */    
	public function listAction()
    {
        $tagId = $this->getRequest()->getParam('tagId');
        $tag = Mage::getModel('cmspro/tag')->load($tagId);

        if(!$tag->getId()) {
            $this->_forward('404');
            return;
        }
        Mage::register('cmspro_current_tag', $tag);

        $this->loadLayout();
        //$this->_initLayoutMessages('checkout/session');
        //$this->_initLayoutMessages('tag/session');
        $this->renderLayout();
    }    
}