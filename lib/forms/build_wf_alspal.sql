
CREATE TABLE wf_alspal (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  fname char(25),
  lname char(25),
  address1 char(40),
  address2 char(40),
  city char(30),
  state char(2),
  zip char(10),
  email char(255),
  comment text,
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;
   
