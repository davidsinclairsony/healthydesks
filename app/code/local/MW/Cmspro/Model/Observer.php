<?php
class MW_Cmspro_Model_Observer
{
	static protected $_singleton = false;
	public function syntaxHighlighter(Varien_Event_Observer $observer)
	{
		if (!self::$_singleton) {
			self::$_singleton = true;

			$layout = $observer->getEvent()->getLayout();
			if ($this->shouldInject($layout->getUpdate()->getHandles())) {
				if ($headBlock = $layout->getBlock('head')) {
					$headBlock->addJs('mw_cmspro/syntaxhighlighter/shCore.js');
					$headBlock->addJs('mw_cmspro/syntaxhighlighter/shAutoloader.js');
					
					$headBlock->addCss('cmspro/css/syntaxhighlighter/shCoreDefault.css');
					$headBlock->addCss('cmspro/css/syntaxhighlighter/shThemeDefault.css');
					
					if ($beforeBodyEnd = $layout->getBlock('before_body_end')) {
						$triggerBlock  = $layout->createBlock('core/template', 'syntaxhighlighter.trigger', array('template' => 'cmspro/syntaxhighlighter.phtml'));

						if ($triggerBlock) {
							$beforeBodyEnd->insert($triggerBlock, '', false, 'syntaxhighlighter.trigger');
						}
					}
				}
			}
		}
	}
	public function shouldInject(array $currentHandles)
	{
		if ($applicableHandles = $this->getApplicableLayoutHandles()) {
			foreach($currentHandles as $handle) {
				if (array_search($handle, $applicableHandles) !== false) {
					return true;
				}
			}
		}

		return false;
	}
	public function getApplicableLayoutHandles()
	{
		$handles = array();
		
		$handles[] = 'cmspro_view_details';
		$handles[] = 'cmspro_index_preview';
		$handles[] = 'cmspro_category_view';
		$handles[] = 'cmspro_search_result';
		$handles[] = 'cmspro_index_index';
		
		
		
		return $handles;
	}
	public function preparePluginConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        $settings = Mage::getModel('cmspro/wysiwyg_config')->getPluginSettings($config);
        $config->addData($settings);
 
        return $this;
    }
	public function checkLicense($o)
	{
		
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled')!=1)
		{
			Mage::helper('cmspro')->disableConfig();
		}
		
	}

}
