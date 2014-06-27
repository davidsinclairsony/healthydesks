<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('cmspro/news_category')} ADD `active` smallint(6) default '1';

");

$installer->endSetup();