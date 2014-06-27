<?php
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmspro/tag')};
CREATE TABLE {$this->getTable('cmspro/tag')} (
  `tag_id` int(3) unsigned NOT NULL auto_increment,
  `name`  varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL default 0,
  `popularity` int(3) NULL default 0,
  PRIMARY KEY (`tag_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;	

DROP TABLE IF EXISTS {$this->getTable('cmspro/news_tag')};
CREATE TABLE {$this->getTable('cmspro/news_tag')} (
  `relation_id` int(11) unsigned NOT NULL auto_increment,
  `news_id` int(11) unsigned NOT NULL ,
  `tag_id`  int(11) unsigned NOT NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`relation_id`),
  CONSTRAINT `FK_CMS_NEWS_TAG_NEWS_ID_CMS_NEWS_NEWS_ID` FOREIGN KEY (`news_id`) REFERENCES `cmspro_news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CMS_NEWS_TAG_TAG_ID_CMS_TAG_TAG_ID` FOREIGN KEY (`tag_id`) REFERENCES `cmspro_tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;	

");

$installer->endSetup();