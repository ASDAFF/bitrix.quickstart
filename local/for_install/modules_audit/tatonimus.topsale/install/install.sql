CREATE TABLE IF NOT EXISTS `b_topsale` (
  `ID` int(11) NOT NULL auto_increment,
  `IBLOCK_ID` int(11) NOT NULL default '0',
  `FIELDS` int(11) NOT NULL default '0',
  `VALUE` varchar(255) default NULL,
  `TRIGGER` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
);
