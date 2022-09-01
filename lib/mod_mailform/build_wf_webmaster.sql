
CREATE TABLE wf_webmaster (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  fullname char(25),
  email char(255),
  comment text,
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
);