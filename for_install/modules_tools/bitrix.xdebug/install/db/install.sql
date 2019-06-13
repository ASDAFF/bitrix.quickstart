CREATE TABLE IF NOT EXISTS b_xdebug (
  trace_id int(11) DEFAULT NULL,
  id varchar(255) DEFAULT NULL,
  time float DEFAULT NULL,
  own_time float DEFAULT NULL,
  include_time float DEFAULT NULL,
  cnt int(11) DEFAULT NULL,
  depth int(11) DEFAULT NULL,
  text_func_name varchar(255) DEFAULT NULL,
  file varchar(255) DEFAULT NULL,
  line int(11) DEFAULT NULL,
  pos int(11) DEFAULT NULL,
  KEY ix_trace (trace_id),
  KEY ix_id (id,trace_id)
);

CREATE TABLE IF NOT EXISTS b_xdebug_trace (
  id int(11) NOT NULL AUTO_INCREMENT,
  trace varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
);
