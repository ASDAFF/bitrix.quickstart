CREATE TABLE IF NOT EXISTS `mlife_asz_basket` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `USERID` int(7) NOT NULL,
  `PROD_ID` int(7) NOT NULL,
  `PARENT_PROD_ID` int(7) DEFAULT NULL,
  `PRICE_VAL` decimal(11,2) NOT NULL,
  `PRICE_CUR` varchar(3) NOT NULL,
  `UPDATE` int(10) NOT NULL,
  `QUANT` decimal(7,2) NOT NULL DEFAULT '1.00',
  `DISCOUNT_VAL` decimal(11,2) DEFAULT NULL,
  `DISCOUNT_CUR` varchar(3) DEFAULT NULL,
  `SITE_ID` varchar(2) DEFAULT NULL,
  `PROD_NAME` varchar(255) NOT NULL,
  `PROD_DESC` varchar(255) DEFAULT NULL,
  `ORDER_ID` int(7) DEFAULT NULL,
  `PROD_LINK` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_country` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `CODE2` varchar(2) NOT NULL,
  `CODE3` varchar(3) NOT NULL,
  `SITEID` varchar(2) NOT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_curency` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `CODE` char(3) NOT NULL,
  `BASE` char(1) NOT NULL,
  `CURS` decimal(10,2) NOT NULL,
  `SITEID` char(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_delivery` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `SITEID` varchar(2) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `ACTIONFILE` varchar(100) NOT NULL,
  `DESC` varchar(1800) DEFAULT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  `PARAMS` varchar(19000) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_options` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(50) NOT NULL,
  `VALUE` varchar(1900) NOT NULL,
  `SITEID` varchar(2) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CODE` (`CODE`,`SITEID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_order` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SITEID` varchar(2) DEFAULT NULL,
  `USERID` int(7) NOT NULL,
  `STATUS` int(7) NOT NULL,
  `PAY_ID` int(7) NOT NULL,
  `DELIVERY_ID` int(7) NOT NULL,
  `PRICE` decimal(11,2) NOT NULL DEFAULT '0.00',
  `DISCOUNT` decimal(11,2) NOT NULL DEFAULT '0.00',
  `TAX` decimal(11,2) NOT NULL DEFAULT '0.00',
  `CURRENCY` varchar(3) NOT NULL,
  `DELIVERY_PRICE` decimal(11,2) NOT NULL DEFAULT '0.00',
  `PAYMENT_PRICE` decimal(11,2) NOT NULL DEFAULT '0.00',
  `DATE` int(11) NOT NULL,
  `PASSW` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `USERID` (`USERID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_order_props` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `SITEID` varchar(2) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `CODE` varchar(50) NOT NULL,
  `PARAMS` varchar(19000) DEFAULT NULL,
  `TYPE` varchar(50) NOT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  `REQ` varchar(1) NOT NULL DEFAULT 'N',
  `DELIVERY` varchar(1) NOT NULL DEFAULT 'N',
  `SORT` int(11) NOT NULL DEFAULT '500',
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_order_propsvalues` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(7) NOT NULL,
  `PROPID` int(7) NOT NULL,
  `VALUE` varchar(3000) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `UID` (`UID`,`PROPID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_order_status` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `SITEID` varchar(2) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `CODE` varchar(1) NOT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  `DESC` varchar(1800) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_paysystem` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `SITEID` varchar(2) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `ACTIONFILE` varchar(500) NOT NULL,
  `DESC` varchar(1800) DEFAULT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  `PARAMS` varchar(19000) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_price` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `IBLOCK` int(7) NOT NULL,
  `PRODID` int(7) NOT NULL,
  `PRICEID` int(11) NOT NULL,
  `PRICEVAL` decimal(11,2) NOT NULL,
  `PRICECUR` char(3) NOT NULL,
  `SORTVAL` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `iblock` (`IBLOCK`,`PRODID`,`PRICEID`),
  KEY `PRODID` (`PRODID`),
  KEY `IBLOCK_2` (`IBLOCK`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_pricetip` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `CODE` char(4) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `BASE` varchar(1) NOT NULL DEFAULT 'N',
  `GROUP` longtext NOT NULL,
  `SITE_ID` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_pricetip_right` (
  `IDTIP` int(11) NOT NULL,
  `IDGROUP` int(11) DEFAULT NULL,
  KEY `idtip` (`IDTIP`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_quant` (
  `IBLOCKID` int(5) NOT NULL,
  `PRODID` int(7) NOT NULL,
  `KOL` int(7) NOT NULL,
  `ZAK` int(7) NOT NULL,
  PRIMARY KEY (`PRODID`),
  KEY `prodid` (`PRODID`),
  KEY `iblockid` (`IBLOCKID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_state` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `CODE2` varchar(2) NOT NULL,
  `CODE3` varchar(3) NOT NULL,
  `COUNTRY` int(7) NOT NULL,
  `ACTIVE` varchar(1) NOT NULL,
  `SORT` int(7) NOT NULL DEFAULT '500',
  PRIMARY KEY (`ID`),
  KEY `COUNTRY` (`COUNTRY`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_user` (
  `UID` int(7) NOT NULL AUTO_INCREMENT,
  `TIME` int(10) NOT NULL,
  `BX_UID` int(7) DEFAULT NULL,
  `SITE_ID` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`UID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_discount` (
  `ID` int(7) NOT NULL AUTO_INCREMENT,
  `IBLOCK_ID` int(7) NOT NULL,
  `CATEGORY_ID` int(11) DEFAULT NULL,
  `PRODUCT_ID` int(11) DEFAULT NULL,
  `NAME` varchar(255) NOT NULL,
  `DESC` varchar(655) DEFAULT NULL,
  `TIP` int(1) NOT NULL,
  `VALUE` decimal(11,2) NOT NULL DEFAULT '0.00',
  `MAXSUMM` decimal(11,2) NOT NULL DEFAULT '0.00',
  `PRIOR` int(3) NOT NULL,
  `PRIORFIX` varchar(1) NOT NULL DEFAULT 'N',
  `DATE_START` datetime NOT NULL,
  `DATE_END` datetime NOT NULL,
  `ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
  `GROUPS` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `IBLOCK_ID` (`IBLOCK_ID`,`CATEGORY_ID`,`PRODUCT_ID`,`DATE_START`,`DATE_END`,`ACTIVE`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_metafilter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IBLOCKID` int(7) NOT NULL,
  `SORT` int(3) NOT NULL,
  `TEMPLATE_TITLE` varchar(655) DEFAULT NULL,
  `TEMPLATE_KEY` varchar(655) DEFAULT NULL,
  `TEMPLATE_DESC` varchar(655) DEFAULT NULL,
  `TEMPLATE_NAME` varchar(655) DEFAULT NULL,
  `TEMPLATE_TEXT` varchar(655) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_metafilter_cat` (
  `ID` int(11) NOT NULL,
  `CATID` int(11) DEFAULT NULL,
  KEY `id` (`ID`)
);
CREATE TABLE IF NOT EXISTS `mlife_asz_metafilter_props` (
  `ID` int(11) NOT NULL,
  `PROPID` int(11) DEFAULT NULL,
  KEY `id` (`ID`)
);