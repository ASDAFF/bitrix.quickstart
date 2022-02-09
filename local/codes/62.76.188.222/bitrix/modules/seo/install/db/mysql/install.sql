create table if not exists b_seo_keywords
(
	ID int(11) not null auto_increment,
	SITE_ID CHAR(2) not null,
	URL varchar(255),
	KEYWORDS text null,
	PRIMARY KEY (ID),
	INDEX ix_b_seo_keywords_url (URL, SITE_ID)
); 