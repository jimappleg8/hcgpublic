<?php

//---------------------------------------------------------------------------
// form definition : site
//---------------------------------------------------------------------------

   $FORM_SAVE_ARRAY = array(
      "SiteID"     => "text",
      "BrandName"  => "text",
      "BaseURL"    => "text",
      "BasePath"   => "text",
      "StoreID"    => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "create table site (".
      "SiteID char(2) not null primary key, ".
      "BrandName char(128) not null, ".
      "BaseURL char(255) not null, ".
      "BasePath char(255) not null, ".
      "StoreID char(128)".
   ")";

   
?>