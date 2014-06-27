<?php  
$installer = $this;

$installer->startSetup();

 $installer->run("
ALTER TABLE {$this->getTable('cmspro/news')} ADD `restrict` smallint(6) NOT NULL default '0';
");

$installer->endSetup(); 