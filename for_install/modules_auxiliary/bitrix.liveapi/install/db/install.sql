CREATE TABLE IF NOT EXISTS b_liveapi(
	module_id varchar(255),
	version varchar(255),
	version_sort int(10),
	type int(10),
	item varchar(255),
	location text,
	etag varchar(255),
	unique index ix_etag (etag),
	key ix_module(module_id, version)
);
