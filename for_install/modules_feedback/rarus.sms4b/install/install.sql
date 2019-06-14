CREATE TABLE IF NOT EXISTS b_sms4b (
  id mediumint(9) NOT NULL auto_increment,
  GUID varchar(255) NOT NULL,
  SenderName varchar(255) NOT NULL,
  Destination varchar(255) NOT NULL,
  StartSend datetime NOT NULL,
  LastModified datetime NOT NULL,
  Status smallint(6) NOT NULL,
  CountPart smallint(6) NOT NULL,
  SendPart smallint(6) NOT NULL,
  CodeType smallint(6) NOT NULL,
  TextMessage text NOT NULL,
  Sale_Order int(10) NOT NULL,
  Posting int(10) NOT NULL,
  Events varchar(255) NOT NULL,
  PRIMARY KEY  (id)
);
CREATE TABLE IF NOT EXISTS b_sms4b_incoming (
  id mediumint(9) NOT NULL auto_increment,
  GUID varchar(36) NOT NULL,
  Moment datetime NOT NULL,
  TimeOff datetime NOT NULL,
  Source varchar(32) NOT NULL,
  Destination varchar(32) NOT NULL,
  Coding tinyint(4) NOT NULL,
  Body text,
  Total smallint(6) NOT NULL,
  Part smallint(6) NOT NULL,
  PRIMARY KEY  (id)
);