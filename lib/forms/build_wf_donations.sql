
CREATE TABLE wf_donations (
  id int(11) NOT NULL auto_increment,
  form_id char(32),
  app_date char(50),
  org_name char(255),
  address text,
  con_name char(255),
  con_phone char(20),
  con_fax char(20),
  con_email char(255),
  event_date char(50),
  request_type text,
  attendance char(10),
  org_desc text,
  org_status char(16),
  501c3_num char(30),
  fed_id_num char(30),
  event_desc text,
  mail_address text,
  signature char(255),
  submit_ts bigint(20) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;
   
