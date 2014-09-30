<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('cmspro/category')} DROP `store_id` ;

ALTER TABLE {$this->getTable('cmspro/news')} DROP `store_id` ;

DROP TABLE IF EXISTS {$this->getTable('cmspro/category_store')};
CREATE TABLE {$this->getTable('cmspro/category_store')}(
	`category_id` int(11) unsigned NOT NULL,
	`store_id` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('cmspro/news_store')};
CREATE TABLE {$this->getTable('cmspro/news_store')}(
	`news_id` int(11) unsigned NOT NULL,
	`store_id` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
?>