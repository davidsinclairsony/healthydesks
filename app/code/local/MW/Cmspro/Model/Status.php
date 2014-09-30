<?php

class MW_Cmspro_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('cmspro')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('cmspro')->__('Disabled')
        );
    }
	static public function toOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('cmspro')->__('Yes'),
            self::STATUS_DISABLED   => Mage::helper('cmspro')->__('No')
        );
    }
}