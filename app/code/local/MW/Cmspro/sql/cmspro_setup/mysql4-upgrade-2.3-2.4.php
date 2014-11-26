<?php

$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();


$installer->getConnection()->addColumn($resource->getTableName('cmspro/news'), 'allow_show_image', 'smallint(6) NOT NULL default "1"');

$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('cmspro/news_store_backup')};
CREATE TABLE {$resource->getTableName('cmspro/news_store_backup')}(
	`news_id` int(11) unsigned NOT NULL,
	`store_id` int(6) NOT NULL,
	FOREIGN KEY (`news_id`) REFERENCES {$resource->getTableName('cmspro/news_backup')} (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('cmspro/news_category_backup')};
CREATE TABLE {$resource->getTableName('cmspro/news_category_backup')}(
	`news_id` int(11) unsigned NOT NULL,
	`category_id` int(6) NOT NULL,
	`active` smallint(6) default '1',
	FOREIGN KEY (`news_id`) REFERENCES {$resource->getTableName('cmspro/news_backup')} (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$resource->getTableName('cmspro/news_backup')};
CREATE TABLE {$resource->getTableName('cmspro/news_backup')} (
  `news_id` int(11) unsigned NOT NULL auto_increment,
  `news_id_parent` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `url_rewrite_id` int(11) unsigned NOT NULL,
  `summary` text NOT NULL default '',
  `content` text NOT NULL default '',
  `identifier` varchar(255) default '',
  `images` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `page_title` text NOT NULL default '',
  `meta_keyword` text NOT NULL default '',
  `meta_description` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `feature` smallint(6) default '0',
  `allowcomment` smallint(6) NOT NULL default '2',
  `allow_show_image` smallint(6) NOT NULL default '1',
  `groups` text NOT NULL default '',
  `users` text NOT NULL default '',
  `restrict` smallint(6) NOT NULL default '0',
  
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");


$installer->endSetup();