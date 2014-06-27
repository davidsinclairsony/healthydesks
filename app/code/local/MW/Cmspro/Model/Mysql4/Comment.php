<?php

class MW_Cmspro_Model_Mysql4_Comment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/comment', 'comment_id');
    }
  
}