<?php

class MW_Cmspro_Model_Type extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function toOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('cmspro')->__('Top Menu'),
            self::STATUS_DISABLED   => Mage::helper('cmspro')->__('Left Menu')
        );
    }
}