<?php 
class MW_Cmspro_Model_Source_Newsbox
{
/* option array of displaying (left,right) of relatednews box for a product */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('cmspro')->__('Yes, show it on the right')),
           
			array('value' => 1, 'label' => Mage::helper('cmspro')->__('Yes, show it on the left')),
			array('value' => 2, 'label' => Mage::helper('cmspro')->__('No, hide it')),
		);
    }
}