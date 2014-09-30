<?php
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmspro/comment')};
CREATE TABLE {$this->getTable('cmspro/comment')} (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
  `news_id` int(11) unsigned NOT NULL,
  `customer_id` int(11) unsigned NULL default 0,
  `fullname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `phone` varchar(20) NULL,
  `created_time` datetime NULL,
  `status` smallint(6) NOT NULL default '2',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;	


DROP TABLE IF EXISTS {$this->getTable('cmspro/relation')};
CREATE TABLE {$this->getTable('cmspro/relation')} (
  `new_id` int(11) unsigned NOT NULL,
  `entity_id`  int(11) unsigned NOT NULL,
  `position`  int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;	

ALTER TABLE {$this->getTable('cmspro/news')} ADD `allowcomment` smallint(6) NOT NULL default '2';
");
$installer->endSetup();