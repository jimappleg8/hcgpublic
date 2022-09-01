
#
# Table structure for table 'faqs'
#

CREATE TABLE faqs (
  faqid int unsigned NOT NULL auto_increment PRIMARY KEY,
  faqlist char(32) NOT NULL,
  title char(255),
  shortquestion text,
  question text,
  answer text,
  flagasnew int,
  status int NOT NULL,
  position int,
  datecreated char(30),
  lastmodified char(30)
) TYPE=MyISAM;

LOAD DATA INFILE "/var/opt/httpd/data/faqs.txt" INTO TABLE faqs FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';

#
# Table structure for table 'faqs_category'
#

# CREATE TABLE faqs_category (
#   categoryid int unsigned NOT NULL auto_increment PRIMARY KEY,
#   faqlist char(2) NOT NULL,
#   categoryname char(255),
#   status int NOT NULL,
#   datecreated char(30),
#   lastmodified char(30)
# ) TYPE=MyISAM;


#
# Table structure for table 'faqs_rotation'
#

# CREATE TABLE faqs_rotation (
#   faqid int unsigned NOT NULL,
#   categoryid int unsigned,
#   rotationid int unsigned
# ) TYPE=MyISAM;