<?php
class MW_Cmspro_Model_Source_Design extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	
    /**
     * Retrieve All Design Theme Options
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $design = Mage::getModel('core/design_package')->getThemeList();
            $options = array();
            foreach ($design as $package => $themes){
                $packageOption = array('label' => $package);
                $themeOptions = array();
                foreach ($themes as $theme) {
                    $themeOptions[] = array(
                        'label' => $theme,
                        'value' => $package . '/' . $theme
                    );
                }
                $packageOption['value'] = $themeOptions;
                $options[] = $packageOption;
            }
            $this->_options = $options;
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value'=>'',
                'label'=>Mage::helper('core')->__('-- Please Select --'))
            );
        }
        return $options;
    }
	/**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        return $value;
    }
 
    /**
     * Retrieve design options array
     *
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
    	//Zend_Debug::dump($this->getAllOptions());die;
        $options = array();

        foreach ($this->getAllOptions() as $value => $label) {
            $options[] = array(
                'label' => $label,
                'value' => $value
            );
        }

        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('page')->__('-- Please Select --')));
        }
		//Zend_Debug::dump($options);die;
        return $options;
    }    
}