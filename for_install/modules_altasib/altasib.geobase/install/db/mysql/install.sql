CREATE TABLE if not exists `altasib_geobase_selected` (
	`ID` int(4) NOT NULL auto_increment,
	`ACTIVE` char(1) default 'Y',
	`SORT` smallint(5) default '500',
	`NAME` varchar(255) default NULL,
	`CODE` varchar(11) default NULL,
	`ID_DISTRICT` varchar(5) default NULL,
	`ID_REGION` varchar(2) default NULL,
	`SOCR` varchar(32) default NULL,
	PRIMARY KEY  (`ID`)
);

CREATE TABLE if not exists `altasib_geobase_kladr_region` (
	`ID` int(3) NOT NULL auto_increment,
	`ACTIVE` char(1) default 'Y',
	`SORT` smallint(5) default '500',
	`NAME` varchar(255) default NULL,
	`FULL_NAME` varchar(255) default NULL,
	`CODE` varchar(2) default NULL,
	`SOCR` varchar(32) default NULL,
	`POSTINDEX` int(6) default NULL,
	PRIMARY KEY  (`ID`)
);
CREATE TABLE if not exists `altasib_geobase_kladr_districts` (
	`ID` int(4) NOT NULL auto_increment,
	`ACTIVE` char(1) default 'Y',
	`SORT` smallint(5) default '500',
	`NAME` varchar(255) default NULL,
	`CODE` varchar(5) default NULL,
	`ID_REGION` varchar(2) default NULL,
	`SOCR` varchar(32) default NULL,
	PRIMARY KEY  (`ID`)
);
CREATE TABLE if not exists `altasib_geobase_kladr_cities` (
	`ID` int(7) NOT NULL auto_increment,
	`ACTIVE` char(1) default 'Y',
	`SORT` smallint(5) default '500',
	`NAME` varchar(255) default NULL,
	`CODE` varchar(11) default NULL,
	`ID_DISTRICT` varchar(5) default NULL,
	`SOCR` varchar(32) default NULL,
	`STATUS` int(1) default NULL,
	`POSTINDEX` int(6) default NULL,
	`SORTINDEX` smallint(3) default NULL,
	PRIMARY KEY  (`ID`)
);