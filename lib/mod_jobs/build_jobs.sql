
#
# Table structure for table 'jobs'
#

CREATE TABLE jobs (
  jobid int unsigned NOT NULL auto_increment PRIMARY KEY,
  siteid char(2) NOT NULL,
  title char(255),
  categoryid int,
  location char(35),
  summary text,
  description text,
  status int,
  datecreated char(30),
  lastmodified char(30)
) TYPE=MyISAM;


#
# Table structure for table 'jobs_category'
#

CREATE TABLE jobs_category (
  categoryid int unsigned NOT NULL auto_increment PRIMARY KEY,
  siteid char(2) NOT NULL,
  categoryname char(255),
  status int NOT NULL,
  datecreated char(30),
  lastmodified char(30)
) TYPE=MyISAM;