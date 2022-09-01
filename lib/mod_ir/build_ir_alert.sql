
CREATE TABLE ir_alert (
  id int(11) NOT NULL auto_increment,
  email char(255),
  alerts char(255),
  datesent date,
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
);