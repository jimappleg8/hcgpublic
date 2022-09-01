
CREATE TABLE sessions (
  SESSKEY char(32) not null,
  EXPIRY int(11) unsigned not null,
  EXPIREREF varchar(64),
  DATA text not null,
  primary key (sesskey)
);