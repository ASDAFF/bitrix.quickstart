-- Dump the table structure  net_city altasib_geobase_mm_city
-- DROP TABLE IF EXISTS `altasib_geobase_mm_city`;
CREATE TABLE IF NOT EXISTS `altasib_geobase_mm_city` (
  `id` int(9) NOT NULL auto_increment,
  `country_id` int(5) default NULL,
  `name_ru` varchar(100) default NULL,
  `name_en` varchar(100) default NULL,
  `region` varchar(2) default NULL,
  `postal_code` varchar(10) default NULL,
  `latitude` varchar(10) default NULL,
  `longitude` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `country_id` (`country_id`),
  KEY `name_ru` (`name_ru`),
  KEY `name_en` (`name_en`)
);

-- DROP TABLE IF EXISTS `altasib_geobase_mm_region`;
CREATE TABLE IF NOT EXISTS `altasib_geobase_mm_region` (
  `id` int(7) NOT NULL auto_increment,
  `country_code` varchar(2) default NULL,
  `region_code` varchar(2) default NULL,
  `lang` varchar(6) default NULL,
  `region_name` varchar(100) default NULL,
  `GeoNames_ID` varchar(9) default NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

-- Dump the table structure (net_country) altasib_geobase_mm_country
-- DROP TABLE IF EXISTS `altasib_geobase_mm_country`;
CREATE TABLE IF NOT EXISTS `altasib_geobase_mm_country` (
  `id` int(5) NOT NULL auto_increment,
  `name_ru` varchar(100) default NULL,
  `name_en` varchar(100) default NULL,
  `code` varchar(2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `code` (`code`),
  KEY `name_en` (`name_en`),
  KEY `name_ru` (`name_ru`)
);

ALTER TABLE `altasib_geobase_selected` ADD COLUMN `NAME_EN` varchar(100) NULL default '' AFTER `NAME`;
ALTER TABLE `altasib_geobase_selected` ADD COLUMN `COUNTRY_CODE` varchar(2) NULL default '' AFTER `ID_REGION`;