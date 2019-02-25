create table if not exists ipol_sdek
(
    ID int(11) NOT NULL auto_increment,
	MESS_ID int(6),
	PARAMS text,
	ORDER_ID int(11),
	SOURCE int(1),
	SDEK_ID int(12),
	STATUS varchar(6),
	MESSAGE text,
	OK varchar(1),
	UPTIME varchar(10),
	PRIMARY KEY(ID),
	INDEX ix_ipol_sdekoi (ORDER_ID)
);