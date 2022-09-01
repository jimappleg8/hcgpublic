USE hcg_public;

drop table pr_product;
create table pr_product (
  ProductID int(11) unsigned not null auto_increment primary key,
  UPC char(11),
  SiteID char(2) not null,
  FilterID int(11),
  Status char(20),
  Verified char(128),
  ProductName char(255),
  LongDescription text,
  Teaser char(255),
  Benefits text,
  AvailableIn char(255),
  Footnotes text,
  Ingredients text,
  NutritionBlend text,
  Standardization char(255),
  Directions text,
  Warning text,
  AllNatural text,
  Gluten char(128),
  OrganicStatement text,
  ThumbFile char(255),
  ThumbWidth int(11) unsigned,
  ThumbHeight int(11) unsigned,
  ThumbAlt char(255),
  SmallFile char(255),
  SmallWidth int(11) unsigned,
  SmallHeight int(11) unsigned,
  SmallAlt char(255),
  LargeFile char(255),
  LargeWidth int(11) unsigned,
  LargeHeight int(11) unsigned,
  LargeAlt char(255),
  NutritionFacts char(255),
  KosherSymbol int(11),
  OrganicSymbol int(11),
  CaffeineFile char(255),
  CaffeineWidth int(11) unsigned,
  CaffeineHeight int(11) unsigned,
  CaffeineAlt char(255),
  StoreSection int(11),
  LocatorCode char(10),
  MenuSubsection char(60),
  DiscontinueDate date,
  Replacements text,
  Explanation text,
  LastModifiedDate date,
  LastModifiedBy char(60),
  MetaMisc text,
  MetaDescription text,
  MetaKeywords text,
  Components int(11),
  ProductType char(20),
  FlavorDescriptor text,
  SortOrder int(11) unsigned,
  FlagAsNew int(11) unsigned,
  Featured int(11) unsigned,
  SpiceLevel char(255),
  Alergens text,
  FeatureFile char(255),
  FeatureWidth int(11) unsigned,
  FeatureHeight int(11) unsigned,
  FeatureAlt char(255),
  BeautyFile char(255),
  BeautyWidth int(11) unsigned,
  BeautyHeight int(11) unsigned,
  BeautyAlt char(255),
  PackageSize char(127),
  ProductGroup char(127)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_product.txt" INTO TABLE pr_product FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


drop table pr_product_category;
create table pr_product_category (
  ProductID int(11) unsigned not null,
  CategoryID int(11) not null
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_product_category.txt" INTO TABLE pr_product_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


drop table pr_category;
create table pr_category (
  CategoryID int(11) unsigned NOT NULL auto_increment PRIMARY KEY,
  SiteID char(2) NOT NULL,
  CategoryCode char(255),
  CategoryName char(255),
  CategoryDescription text,
  CategoryType char(32),
  Status int(11) NOT NULL,
  CategoryParentID int(11),
  CategoryOrder int(11),
  CategoryText text
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_category.txt" INTO TABLE pr_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


drop table site;
create table site (
  SiteID char(2) not null primary key,
  BrandName char(128) not null,
  BaseURL char(255) not null,
  BasePath char(255) not null,
  StoreID char(128)
);

LOAD DATA INFILE "/var/opt/httpd/data/site.txt" INTO TABLE site FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


drop table pr_symbol;
create table pr_symbol (
  SymbolID int(11) unsigned not null auto_increment primary key,
  SymbolFile char(255) not null,
  SymbolWidth int(11) unsigned,
  SymbolHeight int(11) unsigned,
  SymbolAlt char(255)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_symbol.txt" INTO TABLE pr_symbol FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


# drop table pr_filter;
# create table pr_filter (
#   FilterID int unsigned not null auto_increment primary key,
#   SiteID char(2),
#   FilterName char(128)
# );

# LOAD DATA INFILE "/var/opt/httpd/data/pr_filter.txt" INTO TABLE pr_filter FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


# drop table pr_filter_field;
# create table pr_filter_field (
#   FilterID int(11),
#   FieldID int(11)
# );

# LOAD DATA INFILE "/var/opt/httpd/data/pr_filter_field.txt" INTO TABLE pr_filter_field FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


# drop table pr_field;
# create table pr_field (
#   FieldID int(11),
#   FieldName char(255),
#   FieldDescription text,   
#   SaveType char(25),
#   InSaveArray int(11),
#   CreateType text,
#   InDataArray int(11),
#   DisplayInForm int(11),
#   TYPE char(255),
#   NAME" => "UPC",
#   ID char(255),
#   SIZE int(11),
#   MAXLENGTH int(11),
#   LABEL char(255),
#   COLS int(11),
#   ROWS int(11)
# );

# LOAD DATA INFILE "/var/opt/httpd/data/pr_field.txt" INTO TABLE pr_field FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';



