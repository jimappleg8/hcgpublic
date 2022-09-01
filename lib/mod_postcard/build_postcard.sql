USE hcg_public;

DROP TABLE IF EXISTS pc_postcard;
CREATE TABLE pc_postcard (
  PostcardID int(11) unsigned NOT NULL auto_increment,
  PostcardKey varchar(32) default NULL,
  Message text,
  ToName varchar(127) default NULL,
  ToEmail varchar(127) default NULL,
  FromName varchar(127) default NULL,
  FromEmail varchar(127) default NULL,
  QuoteID int(11) default NULL,
  ArtworkID int(11) default NULL,
  DateSent date,
  SiteID char(2) NOT NULL default '',
  PRIMARY KEY  (PostcardID)
);

# LOAD DATA INFILE "/var/opt/httpd/data/pc_postcard.txt" INTO TABLE pc_postcard 
# FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS artwork;
CREATE TABLE artwork (
  ArtworkID int(11) unsigned NOT NULL auto_increment,
  SiteID char(2) NOT NULL default '',
  Status varchar(20) default NULL,
  ArtworkName varchar(127) default NULL,
  Artist varchar(127) default NULL,
  ThumbFile varchar(127) default NULL,
  ThumbWidth int(11) unsigned default NULL,
  ThumbHeight int(11) unsigned default NULL,
  ThumbAlt varchar(127) default NULL,
  SmallFile varchar(127) default NULL,
  SmallWidth int(11) unsigned default NULL,
  SmallHeight int(11) unsigned default NULL,
  SmallAlt varchar(127) default NULL,
  LargeFile varchar(127) default NULL,
  LargeWidth int(11) unsigned default NULL,
  LargeHeight int(11) unsigned default NULL,
  LargeAlt varchar(127) default NULL,
  PRIMARY KEY  (ArtworkID)
);

LOAD DATA INFILE "/var/opt/httpd/data/artwork.txt" INTO TABLE artwork FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS quote;
CREATE TABLE quote (
  QuoteID int(11) unsigned NOT NULL auto_increment,
  Status varchar(20) default NULL,
  SiteID char(2) NOT NULL default '',
  Quotation text,
  Author varchar(127) default NULL,
  AuthorDescription varchar(127) default NULL,
  PRIMARY KEY  (QuoteID)
);

LOAD DATA INFILE "/var/opt/httpd/data/quote.txt" INTO TABLE quote FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pc_artwork_quote;
CREATE TABLE pc_artwork_quote (
  ArtworkID int(11) unsigned NOT NULL default '0',
  QuoteID int(11) NOT NULL default '0'
);

LOAD DATA INFILE "/var/opt/httpd/data/pc_artwork_quote.txt" INTO TABLE pc_artwork_quote FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';

