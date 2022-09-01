<?php

// This data is used to build the pr_field table

$PR_FIELD_DATA_ARRAY[0] = array(
   "FieldID" = 1,
   "FieldName" => "ProductID",
   "FieldDescription" => "The product's record number. Should be assigned automatically by the database.",   
   "SaveType" => "number",
   "InSaveArray" => 0,
   "CreateType" => "int(11) unsigned not null auto_increment primary key",
   "InDataArray" => 0,
   "DisplayInForm" => 0,
);


$PR_FIELD_DATA_ARRAY[1] = array(
   "FieldID" = 2,
   "FieldName" => "UPC",
   "FieldDescription" => "The products Universal Product Code number.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
   "NAME" => "UPC",
   "ID" => "UPC",
   "SIZE" => 20,
   "MAXLENGTH" => 11,
   "LABEL" => "UPC",
);

$PR_FIELD_DATA_ARRAY[2] = array(
   "FieldID" = 3,
   "FieldName" => "SiteID",
   "FieldDescription" => "The two-letter code that identifies which site this product should show up under.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(2) not null",
   "InDataArray" => 1,
   "DisplayInForm" => 0,
);

$PR_FIELD_DATA_ARRAY[3] = array(
   "FieldID" = 4,
   "FieldName" => "FilterID",
   "FieldDescription" => "The record number of the filter to be used to limit the ffields in a given entry form.",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
);

$PR_FIELD_DATA_ARRAY[4] = array(
   "FieldID" = 5,
   "FieldName" => "Status",
   "FieldDescription" => "The display status of the product record. Options are \"active\", \"inactive\", and \"pending\".",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(20)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[42] = array(
   "FieldID" = 43,
   "FieldName" => "ActivateDate",
   "FieldDescription" => "The date a product was/will be automatically activated on the site. The Status field will be automatically changed to \"active\"",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "date",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[42] = array(
   "FieldID" = 43,
   "FieldName" => "DeactivateDate",
   "FieldDescription" => "The date a product was/will be automatically deactivated on the site. The Status field will be automatically changed to \"inactive\"",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "date",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[4] = array(
   "FieldID" = 5,
   "FieldName" => "Completion",
   "FieldDescription" => "The completion status of the product record. Options are \"complete\", and \"partial\". If a product is complete, a product page will be made available with whatever data is available. Otherwise, a link to a product page will not be made available.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(20)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[5] = array(
   "FieldID" = 6,
   "FieldName" => "Distribution",
   "FieldDescription" => "The distribution of the product in stores. Options are \"limited\", \"introduced\", \"normal\", \"discontinued\", and \"unavailable\". Additional information can be entered into the \"Explanation\" field.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(20)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[44] = array(
   "FieldID" = 45,
   "FieldName" => "Explanation",
   "FieldDescription" => "Further information about a products distribution, suggestions for where to look, etc.",
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
   "NAME" => "Explanation",
   "ID" => "Explanation",
   "COLS" => 30,
   "ROWS" => 3,
   "LABEL" => "Explanation",
);

$PR_FIELD_DATA_ARRAY[42] = array(
   "FieldID" = 43,
   "FieldName" => "IntroductionDate",
   "FieldDescription" => "The date a product was/will be introduced to the market.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "date",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[42] = array(
   "FieldID" = 43,
   "FieldName" => "DiscontinueDate",
   "FieldDescription" => "The date a product was/will be discontinued",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "date",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[43] = array(
   "FieldID" = 44,
   "FieldName" => "Replacements",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[5] = array(
   "FieldID" = 6,
   "FieldName" => "Verified",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(128)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[6] = array(
   "FieldID" = 7,
   "FieldName" => "ProductName",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[7] = array(
   "FieldID" = 8,
   "FieldName" => "LongDescription",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[8] = array(
   "FieldID" = 9,
   "FieldName" => "Teaser",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[52] = array(
   "FieldID" = 53,
   "FieldName" => "FlavorDescription",
   "FieldDescription" => "Description of the product's flavor.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
   "NAME" => "FlavorDescription",
   "ID" => "FlavorDescription",
   "COLS" => 30,
   "ROWS" => 3,
   "LABEL" => "Flavor Description",
);

$PR_FIELD_DATA_ARRAY[9] = array(
   "FieldID" = 10,
   "FieldName" => "Benefits",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[10] = array(
   "FieldID" = 11,
   "FieldName" => "NetWt",
   "FieldDescription" => "The package size the product comes in.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[11] = array(
   "FieldID" = 12,
   "FieldName" => "Footnotes",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[12] = array(
   "FieldID" = 13,
   "FieldName" => "Ingredients",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[13] = array(
   "FieldID" = 14,
   "FieldName" => "NutritionBlend",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[14] = array(
   "FieldID" = 15,
   "FieldName" => "Standardization",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[15] = array(
   "FieldID" = 16,
   "FieldName" => "Directions",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[16] = array(
   "FieldID" = 17,
   "FieldName" => "Warning",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[17] = array(
   "FieldID" = 18,
   "FieldName" => "AllNatural",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
);

$PR_FIELD_DATA_ARRAY[18] = array(
   "FieldID" = 19,
   "FieldName" => "Gluten",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(128)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[19] = array(
   "FieldID" = 20,
   "FieldName" => "OrganicStatement",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "area",
);

$PR_FIELD_DATA_ARRAY[20] = array(
   "FieldID" = 21,
   "FieldName" => "ThumbFile",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[21] = array(
   "FieldID" = 22,
   "FieldName" => "ThumbWidth",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[22] = array(
   "FieldID" = 23,
   "FieldName" => "ThumbHeight",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[23] = array(
   "FieldID" = 24,
   "FieldName" => "ThumbAlt",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[24] = array(
   "FieldID" = 25,
   "FieldName" => "SmallFile",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[25] = array(
   "FieldID" = 26,
   "FieldName" => "SmallWidth",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[26] = array(
   "FieldID" = 27,
   "FieldName" => "SmallHeight",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[27] = array(
   "FieldID" = 28,
   "FieldName" => "SmallAlt",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[28] = array(
   "FieldID" = 29,
   "FieldName" => "LargeFile",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[29] = array(
   "FieldID" = 30,
   "FieldName" => "LargeWidth",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[30] = array(
   "FieldID" = 31,
   "FieldName" => "LargeHeight",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[31] = array(
   "FieldID" = 32,
   "FieldName" => "LargeAlt"
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[32] = array(
   "FieldID" = 33,
   "FieldName" => "NutritionFacts",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[33] = array(
   "FieldID" = 34,
   "FieldName" => "KosherSymbol",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[34] = array(
   "FieldID" = 35,
   "FieldName" => "OrganicSymbol",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[35] = array(
   "FieldID" = 36,
   "FieldName" => "CaffeineFile",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[36] = array(
   "FieldID" = 37,
   "FieldName" => "CaffeineWidth",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[37] = array(
   "FieldID" = 38,
   "FieldName" => "CaffeineHeight",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11) unsigned",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[38] = array(
   "FieldID" = 39,
   "FieldName" => "CaffeineAlt",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(255)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[39] = array(
   "FieldID" = 40,
   "FieldName" => "StoreSection",
   "FieldDescription" => "",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[40] = array(
   "FieldID" = 41,
   "FieldName" => "LocatorCode",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(10)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[41] = array(
   "FieldID" = 42,
   "FieldName" => "MenuSubsection",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(60)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
);

$PR_FIELD_DATA_ARRAY[45] = array(
   "FieldID" = 46,
   "FieldName" => "LastModifiedDate",
   "FieldDescription" => "The date when the record was last modified. This should be entered automatically by the script.",
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "date",
   "InDataArray" => 1,
   "DisplayInForm" => 0,
);

$PR_FIELD_DATA_ARRAY[46] = array(
   "FieldID" = 47,
   "FieldName" => "LastModifiedBy",
   "FieldDescription" => "The user id of the person who last modified the record. This should be entered automatically by the script.",
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(60)",
   "InDataArray" => 1,
   "DisplayInForm" => 0,
);

$PR_FIELD_DATA_ARRAY[47] = array(
   "FieldID" = 48,
   "FieldName" => "MetaMisc",
   "FieldDescription" => "Any other metadata tags needed for the page.",
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
   "NAME" => "MetaMisc",
   "ID" => "MetaMisc",
   "COLS" => 30,
   "ROWS" => 3,
   "LABEL" => "Meta Misc",
);

$PR_FIELD_DATA_ARRAY[48] = array(
   "FieldID" = 49,
   "FieldName" => "MetaDescription",
   "FieldDescription" => "Contents of the Description meta tag.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
   "NAME" => "MetaDescription",
   "ID" => "MetaDescription",
   "COLS" => 30,
   "ROWS" => 6,
   "LABEL" => "Meta Description",
);

$PR_FIELD_DATA_ARRAY[49] = array(
   "FieldID" = 50,
   "FieldName" => "MetaKeywords",
   "FieldDescription" => "Contents of the Keywords meta tag.",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "text",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "textarea",
   "NAME" => "MetaKeywords",
   "ID" => "MetaKeywords",
   "COLS" => 30,
   "ROWS" => 3,
   "LABEL" => "Meta Keywords",
);

$PR_FIELD_DATA_ARRAY[50] = array(
   "FieldID" = 51,
   "FieldName" => "Components",
   "FieldDescription" => "Somehow used to deal with mult-SKU products.",   
   "SaveType" => "number",
   "InSaveArray" => 1,
   "CreateType" => "int(11)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
   "NAME" => "Components",
   "ID" => "Components",
   "SIZE" => 20,
   "MAXLENGTH" => 11,
   "LABEL" => "Components",
);

$PR_FIELD_DATA_ARRAY[51] = array(
   "FieldID" = 52,
   "FieldName" => "ProductType",
   "FieldDescription" => "",   
   "SaveType" => "text",
   "InSaveArray" => 1,
   "CreateType" => "char(20)",
   "InDataArray" => 1,
   "DisplayInForm" => 1,
   "TYPE" => "text",
   "NAME" => "ProductType",
   "ID" => "ProductType",
   "SIZE" => 30,
   "MAXLENGTH" => 20,
   "LABEL" => "Product Type",
);


?>