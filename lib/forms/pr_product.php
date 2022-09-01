<?php

//---------------------------------------------------------------------------
// form definition : pr_product
//---------------------------------------------------------------------------

   $FORM_SAVE_ARRAY = array(
      "ProductID"        => "number",
      "UPC"              => "text",
      "SiteID"           => "text",
      "FilterID"         => "number",
      "Status"           => "text",
      "Verified"         => "text",
      "ProductName"      => "text",
      "LongDescription"  => "text",
      "Teaser"           => "text",
      "Benefits"         => "text",
      "AvailableIn"      => "text",
      "Footnotes"        => "text",
      "Ingredients"      => "text",
      "NutritionBlend"   => "text",
      "Standardization"  => "text",
      "Directions"       => "text",
      "Warning"          => "text",
      "AllNatural"       => "text",
      "Gluten"           => "text",
      "OrganicStatement" => "text",
      "ThumbFile"        => "text",
      "ThumbWidth"       => "number",
      "ThumbHeight"      => "number",
      "ThumbAlt"         => "text",
      "SmallFile"        => "text",
      "SmallWidth"       => "number",
      "SmallHeight"      => "number",
      "SmallAlt"         => "text",
      "LargeFile"        => "text",
      "LargeWidth"       => "number",
      "LargeHeight"      => "number",
      "LargeAlt"         => "text",
      "NutritionFacts"   => "text",
      "KosherSymbol"     => "number",
      "OrganicSymbol"    => "number",
      "CaffeineFile"     => "text",
      "CaffeineWidth"    => "number",
      "CaffeineHeight"   => "number",
      "CaffeineAlt"      => "text",
      "StoreSection"     => "number",
      "LocatorCode"      => "text",
      "MenuSubsection"   => "text",
      "DiscontinueDate"  => "text",
      "Replacements"     => "text",
      "Explanation"      => "text",
      "LastModifiedDate" => "text",
      "LastModifiedBy"   => "text",
      "MetaMisc"         => "text",
      "MetaDescription"  => "text",
      "MetaKeywords"     => "text",
      "Components"       => "number",
      "ProductType"      => "text",
      "FlavorDescriptor" => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "create table pr_product (".
      "ProductID int(11) unsigned not null auto_increment primary key, ".
      "UPC char(11), ".
      "SiteID char(2) not null, ".
      "FilterID int(11), ".
      "Status char(20), ".
      "Verified char(128), ".
      "ProductName char(255), ".
      "LongDescription text, ".
      "Teaser char(255), ".
      "Benefits text, ".
      "AvailableIn char(255), ".
      "Footnotes text, ".
      "Ingredients text, ".
      "NutritionBlend text, ".
      "Standardization char(255), ".
      "Directions text, ".
      "Warning text, ".
      "AllNatural text, ".
      "Gluten char(128), ".
      "OrganicStatement text, ".
      "ThumbFile char(255), ".
      "ThumbWidth int(11) unsigned, ".
      "ThumbHeight int(11) unsigned, ".
      "ThumbAlt char(255), ".
      "SmallFile char(255), ".
      "SmallWidth int(11) unsigned, ".
      "SmallHeight int(11) unsigned, ".
      "SmallAlt char(255), ".
      "LargeFile char(255), ".
      "LargeWidth int(11) unsigned, ".
      "LargeHeight int(11) unsigned, ".
      "LargeAlt char(255), ".
      "NutritionFacts char(255), ".
      "KosherSymbol int(11), ".
      "OrganicSymbol int(11), ".
      "CaffeineFile char(255), ".
      "CaffeineWidth int(11) unsigned, ".
      "CaffeineHeight int(11) unsigned, ".
      "CaffeineAlt char(255), ".
      "StoreSection int(11), ".
      "LocatorCode char(10), ".
      "MenuSubsection char(60), ".
      "DiscontinueDate date, ".
      "Replacements text, ".
      "Explanation text, ".
      "LastModifiedDate date, ".
      "LastModifiedBy char(60), ".
      "MetaMisc text, ".
      "MetaDescription text, ".
      "MetaKeywords text, ".
      "Components int(11), ".
      "ProductType char(20), ".
      "FlavorDescriptor text".
   ")";

   
?>