
CREATE TABLE wf_csdistributor (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  fullname char(25),
  company_name char(50),
  email char(255),
  inquiry text,
  datesent date,
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
);