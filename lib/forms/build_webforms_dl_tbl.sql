
CREATE TABLE webforms_dl_tbl (
  form_id varchar(255) NOT NULL default '0',
  download_ts bigint(20) NOT NULL default '0',
  record_id int(11) NOT NULL default '0'
) TYPE=MyISAM;