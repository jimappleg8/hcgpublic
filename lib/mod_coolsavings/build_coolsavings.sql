
CREATE TABLE coolsavings (
  id int(11) NOT NULL auto_increment,
  SiteID char(32),
  FirstName char(25),
  LastName char(25),
  Email char(255),
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
);