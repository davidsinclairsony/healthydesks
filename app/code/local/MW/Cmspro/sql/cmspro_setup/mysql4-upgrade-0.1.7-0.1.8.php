<?php 
$installer = $this;
$installer->startSetup();
 $installer->run("
ALTER TABLE {$this->getTable('cmspro/news')} ADD `groups` text NOT NULL default '';
ALTER TABLE {$this->getTable('cmspro/news')} ADD `users` text NOT NULL default '';
"); 

$installer->endSetup(); 