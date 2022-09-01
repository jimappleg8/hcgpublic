use hcg_public;

#
# Table structure for table 'verbatim'
#

DROP TABLE IF EXISTS vbm_verbatim;
CREATE TABLE vbm_verbatim (
  `VerbatimID` int unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `Status` char(10) NOT NULL default 'inactive',
  `DateSent` date,
  `Letter` text NOT NULL default '',
  `Author` varchar(255) NOT NULL default '',
  `AuthorAddress` text NULL,
  `AuthorCity` varchar(127) NULL,
  `AuthorState` varchar(32) NULL,
  `AuthorZip` varchar(12) NULL,
  `AuthorCountry` varchar(127) NULL,
  `AuthorPhone` varchar(24) NULL,
  `AuthorEmail` varchar(127) NULL,
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL,
  PRIMARY KEY (VerbatimID)
);

LOAD DATA INFILE "/var/opt/httpd/data/vbm_verbatim.txt" INTO TABLE vbm_verbatim FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';

#
# Table structure for table 'vbm_category'
#

DROP TABLE IF EXISTS vbm_category;
CREATE TABLE vbm_category (
  `CategoryID` int unsigned NOT NULL auto_increment,
  `CategoryName` varchar(20) NOT NULL default '',
  `SiteID` varchar(20) NOT NULL default '',
  `CategoryTitle` varchar(255) default NULL,
  `Status` char(10) NOT NULL default 'inactive',
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL,
  PRIMARY KEY (CategoryID)
);

LOAD DATA INFILE "/var/opt/httpd/data/vbm_category.txt" INTO TABLE vbm_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';

#
# Table structure for table 'vbm_verbatim_category'
#

DROP TABLE IF EXISTS vbm_verbatim_category;
CREATE TABLE vbm_verbatim_category (
  `VerbatimID` int unsigned NOT NULL,
  `CategoryID` int unsigned NOT NULL,
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL,
  KEY (VerbatimID)
);

LOAD DATA INFILE "/var/opt/httpd/data/vbm_verbatim_category.txt" INTO TABLE vbm_verbatim_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';
