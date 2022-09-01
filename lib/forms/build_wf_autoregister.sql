
CREATE TABLE wf_autoregister (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  fullname char(128),
  company char(128),
  email char(255),
  dayphone char(14),
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;
   
