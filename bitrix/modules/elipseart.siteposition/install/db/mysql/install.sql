create table if not exists b_ea_siteposition_search_system
(
	ID int(11) not null auto_increment,
	ACTIVE char(1) not null default 'Y',
	NAME varchar(255) not null,
	primary key (ID)
);

create table if not exists b_ea_siteposition_host
(
	ID int(11) not null auto_increment,
	SITE_ID char(2) not null,
	ACTIVE char(1) not null default 'Y',
	NAME varchar(255) not null,
	primary key (ID)
);

create table if not exists b_ea_siteposition_keyword
(
	ID int(11) not null auto_increment,
	HOST_ID int(11) not null,
	REGION_ID int(11) not null default '0',
	ACTIVE char(1) not null default 'Y',
	NAME varchar(255) not null,
	SORT int(11) not null default '500',
	primary key (ID)
);

create table if not exists b_ea_siteposition_position
(
	ID int(11) not null auto_increment,
	SEARCH_ID int(11) not null default '0',
	KEYWORD_ID int(11) not null default '0',
	ACTIVE char(1) not null default 'Y',
	DATE datetime,
	POSITION int(11) not null,
	PAGE varchar(255) null,
	primary key (ID)
);

create table if not exists b_ea_siteposition_region
(
	ID int(11) not null auto_increment,
	CODE int(11) not null default '0',
	primary key (ID)
);
