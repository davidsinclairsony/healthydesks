<?php
class MW_Cmspro_Model_Source_Fieldstoplevel
{
    public function toOptionArray()
    {
        return array(			
            array('value' => 0, 'label' => Mage::helper('cmspro')->__('Level 1')),           
			array('value' => 1, 'label' => Mage::helper('cmspro')->__('Level 2'))
		);
    }
}