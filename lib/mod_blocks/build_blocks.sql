USE hcg_public;

DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  BlockID int(11) unsigned NOT NULL auto_increment,
  SiteID char(2) NOT NULL default '',
  BlockName varchar(255) NOT NULL,
  Language varchar(15) NOT NULL default 'en_us',
  Block text,
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY  (`BlockID`)
) TYPE=MyISAM ;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  PageID int(11) unsigned NOT NULL auto_increment,
  SiteID char(2) NOT NULL default '',
  PageName varchar(255) NOT NULL,
  Language varchar(15) NOT NULL default 'en_us',
  Title varchar(255) NOT NULL default '',
  MetaDescription text,
  MetaKeywords text,
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY  (`PageID`)
) TYPE=MyISAM ;

