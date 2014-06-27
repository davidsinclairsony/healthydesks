<?php  
$installer = $this;

$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmspro_news_news')};
CREATE TABLE {$this->getTable('cmspro_news_news')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `news_id` int(11) unsigned NOT NULL ,
  `related_news_id`  int(11) unsigned NOT NULL,
  `position`  int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;	

");

$installer->endSetup(); 