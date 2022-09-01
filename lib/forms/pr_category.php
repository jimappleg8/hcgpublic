<?php

//---------------------------------------------------------------------------
// form definition : pr_category
//---------------------------------------------------------------------------

   $FORM_SAVE_ARRAY = array(
      "SiteID"              => "text",
      "CategoryCode"        => "text",
      "CategoryName"        => "text",
      "CategoryDescription" => "text",
      "CategoryType"        => "text",
      "Status"              => "number",
   );
   
   $TABLE_CREATE_STATEMENT = "create table pr_category (".
      "CategoryID int(11) unsigned NOT NULL auto_increment PRIMARY KEY, ".
      "SiteID char(2) NOT NULL, ".
      "CategoryCode char(255), ".
      "CategoryName char(255), ".
      "CategoryDescription text, ".
      "CategoryType char(32), ".
      "Status int(11) NOT NULL".
   ")";

   
?>