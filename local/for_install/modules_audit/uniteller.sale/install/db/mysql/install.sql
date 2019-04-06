create table if not exists b_uniteller_agent (
	ID int(18) not null auto_increment,
	ORDER_ID int(18) not null,
	INSERT_DATATIME timestamp not null,
	TYPE_ERROR varchar(20) not null,
	TEXT_ERROR varchar(255) not null,
	primary key (ID)
);