<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('cmspro/category')} ADD `order` int(11) default 0;

");

$installer->endSetup();
?>