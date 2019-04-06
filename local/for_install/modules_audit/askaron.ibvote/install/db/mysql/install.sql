create table if not exists b_askaron_ibvote_event (
	ID int(18) not null auto_increment,
	ELEMENT_ID int(18) not null default '0',
	ANSWER int(18),
	DATE_VOTE datetime not null default '0000-00-00 00:00:00',
	IP varchar(15),
	USER_ID int(18),
	STAT_SESSION_ID int(18),
	primary key (ID),
	index IX_ELEMENT_ID_IP (ELEMENT_ID,IP),
	index IX_ELEMENT_ID_USER_ID (ELEMENT_ID,USER_ID)
);