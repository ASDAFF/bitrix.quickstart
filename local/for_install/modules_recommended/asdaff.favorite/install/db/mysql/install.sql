create table if not exists b_asdaff_favorite_folders
(
	ID int not null auto_increment,
	`NAME` varchar(255) not null,
	CODE varchar(50) not null,
	USER_ID int not null,
	`DEFAULT` char(1) not null default 'N',
	primary key (ID)
);
alter table `b_asdaff_favorite_folders` add key `ASDAFF_USER_ID` (`USER_ID`);
alter table `b_asdaff_favorite_folders` add key `ASDAFF_CODE` (`CODE`(5));

create table if not exists b_asdaff_favorite_types
(
	CODE varchar(50) not null,
	`NAME` varchar(255) not null,
	`MODULE` varchar(20) not null default 'iblock',
	primary key (CODE)
);

create table if not exists b_asdaff_favorite_likes
(
	ID int not null auto_increment,
	ELEMENT_ID int not null,
	FOLDER_ID int not null,
	USER_ID int not null,
	CODE varchar(50) not null,
	primary key (ID)
);
alter table `b_asdaff_favorite_likes` add key `ASDAFF_ELEMENT_ID` (`ELEMENT_ID`);
alter table `b_asdaff_favorite_likes` add key `ASDAFF_FOLDER_ID` (`FOLDER_ID`);

create table if not exists b_asdaff_favorite_types_ref
(
	`CODE` varchar(50) not null,
	`REF` varchar(10) not null,
	key (`CODE`)
);