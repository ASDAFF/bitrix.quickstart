drop table if exists ss_geoip_ip;
create table if not exists ss_geoip_city (
   ID int(18) not null auto_increment,
   COUNTRY_ID char(2) not null,
   REGION varchar(200),
   NAME varchar(255),
   XML_ID varchar(255),
   primary key (ID),
   index UX_B_STAT_CITY(COUNTRY_ID, REGION(50), NAME(50)),
   index IX_B_STAT_CITY_XML_ID(XML_ID)
);
create table if not exists ss_geoip_ip (
   START_IP bigint(18) not null,
   END_IP bigint(18) not null,
   COUNTRY_ID char(2) not null,
   CITY_ID int(18) not null,
   primary key (START_IP),
   index IX_B_STAT_CITY_IP_END_IP(END_IP)
);
create table if not exists ss_geoip_country (
   ID char(2) not null,
   SHORT_NAME char(3),
   NAME varchar(50),
   RU_NAME varchar(50),
   primary key (ID)
);