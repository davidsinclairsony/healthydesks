<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'cmspro/news_design'
 */

$installer->run("

ALTER TABLE {$this->getTable('cmspro/news_design')} MODIFY COLUMN `news_id` int(11) unsigned NOT NULL	

");

$installer->endSetup(); 