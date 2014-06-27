<?php
class MW_Cmspro_Model_Newsdesign extends Mage_Core_Model_Abstract
{
	const USE_DEFAULT = 1;
	const USE_OWN = 2;
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cmspro/newsdesign');
    }	
}