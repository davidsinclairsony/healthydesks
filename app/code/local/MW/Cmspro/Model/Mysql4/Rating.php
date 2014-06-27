<?php
class MW_Cmspro_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract{
	
	public function _construct()
    {    
        // Note that the cmspro_id refers to the key field in your database table.
        $this->_init('cmspro/rating', 'rating_id');
    }
}