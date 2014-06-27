<?php

class MW_Cmspro_Model_Newsbackup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cmspro/newsbackup');
    }
}