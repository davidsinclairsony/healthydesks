<?php
class MW_Cmspro_Block_Adminhtml_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('tag_form');
        $this->setTitle(Mage::helper('cmspro')->__('Block Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('cmspro_tag');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cmspro')->__('General Information')));

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', array(
                'name' => 'tag_id',
            ));
        }

        $fieldset->addField('form_key', 'hidden', array(
            'name'  => 'form_key',
            'value' => Mage::getSingleton('core/session')->getFormKey(),
        ));

       /* $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store')
        ));*/

        $fieldset->addField('name', 'text', array(
            'name' => 'tag_name',
            'label' => Mage::helper('cmspro')->__('Tag Name'),
            'title' => Mage::helper('cmspro')->__('Tag Name'),
            'required' => true,
            'after_element_html' => ' [GLOBAL]',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('cmspro')->__('Status'),
            'title' => Mage::helper('cmspro')->__('Status'),
            'name' => 'tag_status',
            'required' => true,
            /*'options' => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('cmspro')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('cmspro')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('cmspro')->__('Approved'),
            ),*/
        	'options'  => $this->helper('cmspro/data')->getStatusesArray(),
            'after_element_html' => ' [GLOBAL]',
        ));

        /*$fieldset->addField('base_popularity', 'text', array(
            'name' => 'base_popularity',
            'label' => Mage::helper('cmspro')->__('Base Popularity'),
            'title' => Mage::helper('cmspro')->__('Base Popularity'),
            'after_element_html' => ' [STORE VIEW]',
        ));*/

        if (!$model->getId() && !Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $model->setStatus(MW_Cmspro_Model_Tag::STATUS_ENABLED);
        }

        if ( Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getTagData());
            Mage::getSingleton('adminhtml/session')->setTagData(null);
        } else {
            $form->addValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}