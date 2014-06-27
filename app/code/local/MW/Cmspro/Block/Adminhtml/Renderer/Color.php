<?php
class MW_Cmspro_Block_Adminhtml_Renderer_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setStyle('width:150px;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $from = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();
        return '<div class="text-color-preview">'.$from. '</div> '. ' <div id="mw-color-preview" class="color-preview"></div><p style ="clear: both;"></p>';
    }
}