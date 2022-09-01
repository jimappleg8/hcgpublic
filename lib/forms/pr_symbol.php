<?php

//---------------------------------------------------------------------------
// form definition : pr_symbol
//---------------------------------------------------------------------------

   $FORM_SAVE_ARRAY = array(
      "SymbolID"     => "number",
      "SymbolFile"   => "text",
      "SymbolWidth"  => "number",
      "SymbolHeight" => "number",
      "SymbolAlt"    => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "create table pr_symbol (".
      "SymbolID int(11) unsigned not null auto_increment primary key, ".
      "SymbolFile char(255) not null, ".
      "SymbolWidth int(11) unsigned, ".
      "SymbolHeight int(11) unsigned, ".
      "SymbolAlt char(255)".
   ")";

   
?>