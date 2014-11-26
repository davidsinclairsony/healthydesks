<?php
class MW_Cmspro_Block_Adminhtml_News_Edit_Tab_Design
    extends Mage_Adminhtml_Block_Widget_Form
    	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('news_');

        $model = Mage::registry('cmspronews_data');

        $activeFieldset = $form->addFieldset('active_fieldset', array(
            'legend' => Mage::helper('cmspro')->__('Use default design'),
            'class'  => 'fieldset-wide',
            'disabled'  => false
        ));
        
         $activeFieldset->addField('is_default_layout', 'select', array(
            'name'     => 'is_default_layout',
            'label'    => Mage::helper('cmspro')->__('Use default design'),
            'values'   => Mage::getSingleton('cmspro/status')->toOptionArray(),
            'disabled' => false
        ));
        
        $layoutFieldset = $form->addFieldset('layout_fieldset', array(
            'legend' => Mage::helper('cmspro')->__('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => false
        ));

        $layoutFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('cmspro')->__('Layout'),
            'required' => false,
            //'values'   => Mage::getSingleton('cmspro/status')->toOptionArray(),
            'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
            'disabled' => false
        ));
       /* if (!$model->getId()) {
            $model->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
        }*/

        $layoutFieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('cmspro')->__('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => false
        ));

        $designFieldset = $form->addFieldset('design_fieldset', array(
            'legend' => Mage::helper('cmspro')->__('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => false
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $designFieldset->addField('custom_theme_from', 'date', array(
            'name'      => 'custom_theme_from',
            'label'     => Mage::helper('cmspro')->__('Custom Design From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => false
        ));

        $designFieldset->addField('custom_theme_to', 'date', array(
            'name'      => 'custom_theme_to',
            'label'     => Mage::helper('cmspro')->__('Custom Design To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => false
        ));

        $designFieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => Mage::helper('cmspro')->__('Custom Theme'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
            'disabled'  => false
        ));


        $designFieldset->addField('custom_root_template', 'select', array(
            'name'      => 'custom_root_template',
            'label'     => Mage::helper('cmspro')->__('Custom Layout'),
            'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
            'disabled'  => false
        ));

        $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
            'name'      => 'custom_layout_update_xml',
            'label'     => Mage::helper('cmspro')->__('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => false
        ));

        Mage::dispatchEvent('adminhtml_news_edit_tab_design_prepare_form', array('form' => $form));
		
        $id = $this->getRequest()->getParam('id');
        $designModel = Mage::getModel('cmspro/newsdesign')->load($id); 
        if ($designModel->getId()) {
        	$form->setValues($designModel->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cmspro')->__('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cmspro')->__('Design');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
