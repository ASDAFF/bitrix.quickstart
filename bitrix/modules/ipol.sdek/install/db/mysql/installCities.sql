create table if not exists ipol_sdekcities
(
	ID int(5) NOT NULL auto_increment,
	BITRIX_ID int(5),
	SDEK_ID int(5),
	NAME varchar(50),
	REGION varchar(20),
	PAYNAL varchar(10),
	COUNTRY varchar(3),
	PRIMARY KEY(ID),
	INDEX ix_ipol_sC_BID (BITRIX_ID)
);