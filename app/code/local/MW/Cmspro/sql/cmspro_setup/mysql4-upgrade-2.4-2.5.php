<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();


$installer->getConnection()->addColumn($resource->getTableName('cmspro/news'), 'author', 'varchar(255) NOT NULL default ""');
$installer->getConnection()->addColumn($resource->getTableName('cmspro/news_backup'), 'author', 'varchar(255) NOT NULL default ""');

$installer->endSetup();