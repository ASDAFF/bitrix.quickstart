DELETE FROM b_agent WHERE MODULE_ID='altasib.geobase';
DROP TABLE if exists altasib_geobase_selected;

DROP TABLE if exists altasib_geobase_kladr_region;
DROP TABLE if exists altasib_geobase_kladr_districts;
DROP TABLE if exists altasib_geobase_kladr_cities;

DROP TABLE if exists altasib_geobase_mm_city;
DROP TABLE if exists altasib_geobase_mm_region;
DROP TABLE if exists altasib_geobase_mm_country;