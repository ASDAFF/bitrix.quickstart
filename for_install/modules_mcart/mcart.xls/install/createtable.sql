CREATE TABLE IF NOT EXISTS `mcart_xls` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`name_field` int(11),
	`identify` varchar(255),
	`sheet_id` int(11),
	`iblock_id` int(11),
	`section_id` int(11),
	`data_row` int(11),
	`title_row` int(11),
	`diapazone_a` varchar(2),
	`diapazone_z` varchar(2),
	`sku_iblock_id` int(11),
	`cml2_link_code` varchar(20),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2;

CREATE TABLE IF NOT EXISTS `mcart_xls_fields` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`profile_id` int(11) NOT NULL,
	`col_id` int(11),
	`key2` int(11),
	`field_code` varchar(255),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2;