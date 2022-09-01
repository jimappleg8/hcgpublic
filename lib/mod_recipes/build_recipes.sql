USE hcg_public;

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE `recipes` (
  `RecipeID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` char(2) NOT NULL default '',
  `FlagAsNew` tinyint(4) default '0',
  `Active` tinyint(4) NOT NULL default '0',
  `Category` varchar(127) default NULL,
  `Title` varchar(127) default NULL,
  `Makes` varchar(127) default NULL,
  `Teaser` text,
  `Img` varchar(127) default NULL,
  `ImgWidth` int(11) unsigned default NULL,
  `ImgHeight` int(11) unsigned default NULL,
  `ImgAlt` varchar(127) default NULL,
  `Ingredients` text,
  `Instructions` text,
  `Note` text,
  `Vegetarian` tinyint(4) default '0',
  `Spicy` tinyint(4) default '0',
  `Language` varchar(5) default NULL,
  PRIMARY KEY  (`RecipeID`)
) TYPE=MyISAM ;

LOAD DATA INFILE "/var/opt/httpd/data/recipes.txt" INTO TABLE recipes FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';
