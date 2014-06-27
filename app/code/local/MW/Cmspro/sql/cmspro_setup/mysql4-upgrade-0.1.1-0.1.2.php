<?php
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('cmspro_news')} DROP `category_id` ;

DROP TABLE IF EXISTS {$this->getTable('cmspro/news_category')};
CREATE TABLE {$this->getTable('cmspro/news_category')}(
	`news_id` int(11) unsigned NOT NULL,
	`category_id` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
?>