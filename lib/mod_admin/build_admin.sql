USE hcg_public;


DROP TABLE IF EXISTS category;
CREATE TABLE category (
  CategoryID
  Parent
  SiteID char(2),
  ModuleID char(32),
  CategoryName varchar(255),
  Description text,
  LongDescription text,
  Status
  Sort
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (CategoryID)
);


DROP TABLE IF EXISTS domain;
CREATE TABLE domain (
  DomainName char(127),
  SiteID char(2),
  SiteType char(5) enum('live','stage', 'dev') NOT NULL default 'live',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
);


DROP TABLE IF EXISTS help;
CREATE TABLE help (
  TaskID varchar(64) NOT NULL default '',
  HelpText text,
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (TaskID)
);

# created on cheetah

DROP TABLE IF EXISTS menu;
CREATE TABLE menu (
  MenuID int(11) UNSIGNED NOT NULL auto_increment,
  Parent int(11) UNSIGNED NOT NULL default '0',
  Lft int(11) UNSIGNED NOT NULL default '0',
  Rgt int(11) UNSIGNED NOT NULL default '0',
  SiteID char(2),
  MenuText varchar(63) NOT NULL default '',
  LinkText varchar(63) NOT NULL default '',
  Description text,
  URL varchar(255) NOT NULL default '',
  Sort int(11) UNSIGNED NOT NULL default '0',
  NoDelete tinyint(1) unsigned NOT NULL default '0',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (MenuID)
);


DROP TABLE IF EXISTS module;
CREATE TABLE module (
  ModuleID char(32) NOT NULL,
  Description
  Directory
  Version
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (ModuleID)
);


DROP TABLE IF EXISTS nav_button;
CREATE TABLE nav_button (
  ButtonID
  TaskID varchar(64) NOT NULL default '',
  ButtonText
  ButtonImage
  ContextPreselect tinyint(1) unsigned NOT NULL default '0',
  Sort int(11) NOT NULL default '0',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (ButtonID)
);


DROP TABLE IF EXISTS pattern;
CREATE TABLE pattern (
  PatternID int(11) UNSIGNED NOT NULL,
  Description,
  LongDescription text,
  VisibleScreens,
  ContextPreselection,
  KeepData,
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (PatternID)
);


DROP TABLE IF EXISTS role;
CREATE TABLE role (
  RoleID int(11) UNSIGNED NOT NULL auto_increment,
  Name,
  Description,
  StartTask int(11) UNSIGNED,
  GlobalAccess tinyint(1) unsigned NOT NULL default '0',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (RoleID)
);


DROP TABLE IF EXISTS role_task;
CREATE TABLE role_task (
  RoleID int(11) UNSIGNED NOT NULL,
  TaskID varchar(64) NOT NULL default '',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (RoleID)
);

# created on Cheetah

DROP TABLE IF EXISTS site;
CREATE TABLE site (
  SiteID char(2) NOT NULL default '',
  BrandName char(127) NOT NULL default '',
  LiveURL char(127) NOT NULL default '',
  LiveDir char(25) NOT NULL default '',
  StageURL char(127) NOT NULL default '',
  StageDir char(25) NOT NULL default '',
  DevURL char(127) NOT NULL default '',
  DevDir char(25) NOT NULL default '',
  StoreID char(128) default NULL,
  AdmMenuRoot int(11) NOT NULL default '0',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY  (SiteID)
);


DROP TABLE IF EXISTS role_task_field;
CREATE TABLE role_task_field (
  RoleID int(11) UNSIGNED NOT NULL,
  FieldID
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
);


DROP TABLE IF EXISTS task;
CREATE TABLE task (
  TaskID varchar(64) NOT NULL default '',
  Description,
  ButtonText
  Type char(4) enum('menu','proc') NOT NULL default 'menu',
  PatternID,
  ModuleID char(32),
  ScriptID,
  InitialPassthru,
  FixedSelection,
  TempSelection,
  Settings,
  OrderBy,
  KeepData tinyint(1) unsigned NOT NULL default '0',,
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (TaskID)
);


DROP TABLE IF EXISTS task_field;
CREATE TABLE task_field (
  FieldID,
  TaskID varchar(64) NOT NULL default '',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (FieldID)
);  


DROP TABLE IF EXISTS user;
CREATE TABLE user (
  UserID int(11) UNSIGNED NOT NULL,
  Username varchar(20) NOT NULL default '',
  Password varchar(20) NOT NULL default '',
  AuthSource char(5) enum('ldap','mysql') NOT NULL default 'mysql',
  FirstName varchar(100) default NULL,
  LastName varchar(100) default NULL,
  Email varchar(200) default NULL,
  RoleID int(11) UNSIGNED NOT NULL default '0',
  LanguageCode char(6) NOT NULL default '',
  FirstLogin datetime default NULL,
  LastLogin datetime default NULL,
  PasswordChangeDate datetime default NULL,
  PasswordCount int(11),
  InUse tinyint(1) unsigned NOT NULL default '0',
  UserDisabled tinyint(1) unsigned NOT NULL default '0',
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (UserID)
);

