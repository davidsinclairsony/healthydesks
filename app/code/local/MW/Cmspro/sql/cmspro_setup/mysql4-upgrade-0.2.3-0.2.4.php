<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('cmspro/news_tag')} DROP FOREIGN KEY `FK_CMS_NEWS_TAG_NEWS_ID_CMS_NEWS_NEWS_ID`;
ALTER TABLE {$this->getTable('cmspro/news_tag')} ADD CONSTRAINT `FK_CMS_NEWS_TAG_NEWS_ID_CMS_NEWS_NEWS_ID` FOREIGN KEY (`news_id`) REFERENCES {$this->getTable('cmspro/news')} (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('cmspro/news_tag')} DROP FOREIGN KEY `FK_CMS_NEWS_TAG_TAG_ID_CMS_TAG_TAG_ID`;
ALTER TABLE {$this->getTable('cmspro/news_tag')} ADD CONSTRAINT `FK_CMS_NEWS_TAG_TAG_ID_CMS_TAG_TAG_ID` FOREIGN KEY (`tag_id`) REFERENCES {$this->getTable('cmspro/tag')} (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE {$this->getTable('cmspro/news_design')} DROP FOREIGN KEY `FK_CMSPRO_NEWS_DESIGN_NEWS_ID_CMSPRO_NEWS_NEWS_ID`;
ALTER TABLE {$this->getTable('cmspro/news_design')} ADD CONSTRAINT `FK_CMSPRO_NEWS_DESIGN_NEWS_ID_CMSPRO_NEWS_NEWS_ID` FOREIGN KEY (`news_id`) REFERENCES {$this->getTable('cmspro/news')} (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
");

$installer->endSetup(); 