CREATE TABLE IF NOT EXISTS `b_sitemap_property` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SIT` varchar(3) NULL,
  `TYPE` tinyint(1) NOT NULL,
  `CODE` varchar(250) NOT NULL,
  `NAME` varchar(250) NOT NULL,
  `PATH` text NOT NULL,
  `MOD` tinyint(3) NOT NULL,
  `FREQ` varchar(50) NOT NULL DEFAULT 'none',
  `PRIORITY` float NOT NULL,
  `HTTPS` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
);


CREATE TABLE IF NOT EXISTS `b_sitemap_generation` (
  `NAME` varchar(10) NOT NULL,
  `VALUE` varchar(5) NOT NULL DEFAULT 'none'
);

INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('TIME','0'),('MOD','0'),('PRIORITY','0'),('GZIP','0'),('FREQ','none'),('PROTOCOL','0');