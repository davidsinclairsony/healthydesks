<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmspro/category')};
CREATE TABLE {$this->getTable('cmspro/category')} (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `url_rewrite_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `root_path` text NOT NULL default '',
  `level` int(11) unsigned NOT NULL default 1,
  `identifier` varchar(255) default '',
  `description` text default '',
  `images` varchar(255) NOT NULL default '',
  `store_id` int(11) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `meta_keyword` text NOT NULL default '',
  `meta_description` text NOT NULL default '',
  `page_title` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('cmspro/news')};
CREATE TABLE {$this->getTable('cmspro/news')} (
  `news_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `url_rewrite_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `store_id` int(11) NOT NULL default 0,
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
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DELETE FROM {$this->getTable('core/url_rewrite')} where target_path LIKE 'cmspro/%';
INSERT INTO {$this->getTable('core/url_rewrite')}(store_id,id_path,request_path,target_path,is_system) VALUES('0','news','news','cmspro/index/index',0);
INSERT INTO {$this->getTable('cmspro/category')} SET 
	url_rewrite_id= (SELECT max(url_rewrite_id) from {$this->getTable('core/url_rewrite')}),
	name='News',
	level='1',
	parent_id=0,
	root_path='1/',
	status=1,
	meta_description='News',
	meta_keyword = 'News',
	page_title = 'News' ;

");


$installer->endSetup();