CREATE TABLE IF NOT EXISTS `b_webdebug_pageprops` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROPERTY` varchar(255) NOT NULL,
  `SITE` varchar(2) DEFAULT NULL,
  `TYPE` varchar(255) NOT NULL,
  `DATA` longtext NOT NULL,
  PRIMARY KEY (`ID`)
)