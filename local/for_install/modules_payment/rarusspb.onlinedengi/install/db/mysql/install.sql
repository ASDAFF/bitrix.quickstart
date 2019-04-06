create table b_onlinedengi_rates (
	ID int unsigned not null auto_increment,
	ORDER_ID int(11) not null,
	PAYMENT_MODE_TYPE tinyint not null,
	RATE_VALUE decimal(18, 6) not null default 0.00,
	TIMESTAMP datetime,
	primary key (ID),
	index (ORDER_ID, PAYMENT_MODE_TYPE)
);
