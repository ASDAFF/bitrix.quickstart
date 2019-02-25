create table if not exists b_ambersite_quickpay
(
	ID int(11) not null auto_increment,
	CODE varchar(255),
	PRODUCT varchar(255),
	COUNT int(11),
	FIO varchar(255),
	PHONE varchar(255),
	EMAIL varchar(255),
	KOMM text,
	PAYTYPE varchar(2) not null default 'AC',
	SUM INT(18) NOT NULL DEFAULT 0,
	PAID char(1) not null default 'N',
	DATE datetime,
	primary key (ID)
);