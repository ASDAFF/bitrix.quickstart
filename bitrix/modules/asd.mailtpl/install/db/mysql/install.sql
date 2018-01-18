create table if not exists b_asd_mailtpl
(
	`ID` int not null auto_increment,
	`NAME` varchar(255) not null,
	`HEADER` text,
	`FOOTER` text,
	`TYPE` char(4) not null default 'text',
	`SETTINGS` text,
	primary key (ID)
);
create table if not exists b_asd_mailtpl_events
(
	`TPL_ID` int not null,
	`EVENT` varchar(50)
);