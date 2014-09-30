<?php
class MW_Cmspro_Model_Source_Fieldsdeeplevel
{
    public function toOptionArray()
    {
        return array(
			array('value' => 0, 'label' => Mage::helper('cmspro')->__('No limit')),
            array('value' => 1, 'label' => Mage::helper('cmspro')->__('Level 1')),           
			array('value' => 2, 'label' => Mage::helper('cmspro')->__('Level 2')),
            array('value' => 3, 'label' => Mage::helper('cmspro')->__('Level 3')),
			array('value' => 4, 'label' => Mage::helper('cmspro')->__('Level 4')),
			array('value' => 5, 'label' => Mage::helper('cmspro')->__('Level 5'))
		);
    }
}