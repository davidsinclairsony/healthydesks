<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'cmspro/news_design'
 */

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cmspro/news_design')};
CREATE TABLE {$this->getTable('cmspro/news_design')} (
  `news_id` int(11) unsigned NOT NULL auto_increment,
  `root_template`  varchar(255) NULL,
  `is_default_layout` smallint(6) unsigned NULL,
  `layout_update_xml` text NULL,
  `custom_theme`  varchar(100)  NULL,
  `custom_root_template`  varchar(100)  NULL,
  `custom_layout_update_xml`  text NULL,
  `custom_theme_from`  datetime NULL,
  `custom_theme_to`  datetime NULL,
  PRIMARY KEY (`news_id`),
  CONSTRAINT `FK_CMSPRO_NEWS_DESIGN_NEWS_ID_CMSPRO_NEWS_NEWS_ID` FOREIGN KEY (`news_id`) REFERENCES `cmspro_news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;	

");

$installer->endSetup(); 