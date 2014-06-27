<?php
class MW_Cmspro_Model_Mysql4_Newsdesign extends Mage_Core_Model_Mysql4_Abstract
{
	
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
		$this->_isPkAutoIncrement = false;
        $this->_init('cmspro/news_design', 'news_id');
    }	
	
    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return MW_Cmspro_Model_Mysql4_Newsdesign
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        return parent::_beforeSave($object);
    }	
}