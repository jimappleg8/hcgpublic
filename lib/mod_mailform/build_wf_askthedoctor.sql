
CREATE TABLE wf_askthedoctor (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  doctor char(127),
  fullname char(25),
  email char(255),
  question text,
  datesent date,
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
);