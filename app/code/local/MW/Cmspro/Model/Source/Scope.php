<?php
class MW_Cmspro_Model_Source_Scope
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'both', 'label' => Mage::helper('cmspro')->__('Both case')),
            array('value' => 'guest', 'label' => Mage::helper('cmspro')->__('Guest Only')),           
			array('value' => 'login', 'label' => Mage::helper('cmspro')->__('Logged in Only')),
			array('value' => 'norequire', 'label' => Mage::helper('cmspro')->__('No require'))
		);
    }
}