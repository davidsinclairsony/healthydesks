<?php
class MW_Cmspro_Model_Source_Fieldsleft
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('cmspro')->__('Yes, Accordion')),
           
			array('value' => 2, 'label' => Mage::helper('cmspro')->__('Yes, Drop down')),
           
			array('value' => 0, 'label' => Mage::helper('cmspro')->__('No'))
		);
    }
}