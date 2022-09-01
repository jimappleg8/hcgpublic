<?php

//---------------------------------------------------------------------------
// form definition : wf_autoregister
//---------------------------------------------------------------------------

   $FORM_FIELDS_ARRAY[0] = array(
      "TYPE" => "text",
      "NAME" => "fullname",
      "ID" => "fullname",
      "SIZE" => 30,
      "MAXLENGTH" => 128,
      "Capitalization" => "words",
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter your name.",
      "LABEL" => "<u>F</u>ull Name",
      "ACCESSKEY" => "F",
   );

   $FORM_FIELDS_ARRAY[1] = array(
      "TYPE" => "text",
      "NAME" => "company",
      "ID" => "company",
      "SIZE" => 30,
      "MAXLENGTH" => 128,
      "Capitalization" => "words",
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter your company name.",
      "LABEL" => "Company/<u>A</u>ffiliation",
      "ACCESSKEY" => "A",
   );

   $FORM_FIELDS_ARRAY[2] = array(
      "TYPE" => "text",
      "NAME" => "email",
      "ID" => "email",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsNotEmpty" => 1,
      "ValidateAsNotEmptyErrorMessage" => "You must enter a valid email address.",
      "ValidateAsEmail" => 1,
      "ValidateAsEmailErrorMessage" => "This does not appear to be a valid email.",
      "LABEL" => "<u>E</u>-Mail",
      "ACCESSKEY" => "E",
   );

   $FORM_FIELDS_ARRAY[3] = array(
      "TYPE" => "text",
      "NAME" => "email2",
      "ID" => "email2",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsEqualTo" => "email",
      "ValidationErrorMessage" => "The two e-mail addresses are not the same.",
      "LABEL" => "<u>C</u>onfirm E-Mail",
      "ACCESSKEY" => "C",
   );

   $FORM_FIELDS_ARRAY[4] = array(
      "TYPE" => "text",
      "NAME" => "dayphone",
      "ID" => "dayphone",
      "SIZE" => 30,
      "MAXLENGTH" => 14,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a daytime phone number.",
      "LABEL" => "<u>D</u>aytime Phone",
      "ACCESSKEY" => "D",
   );
   
   $FORM_FIELDS_ARRAY[5] = array(
      "TYPE" => "submit",
      "ID" => "button_register",
      "VALUE" => "Register",
      "ACCESSKEY" => "R",
   );
   
   $FORM_FIELDS_ARRAY[6] = array(
      "TYPE" => "reset",
      "ID" => "button_reset",
      "VALUE" => "Reset",
      "ACCESSKEY" => "s",
   );
   
   $FORM_SAVE_ARRAY = array(
      "fullname" => "text",
      "company"  => "text",
      "email"    => "text",
      "dayphone" => "text"
   );

   $TABLE_CREATE_STATEMENT = "CREATE TABLE wf_autoregister (".
      "id int(11) NOT NULL auto_increment, ".
      "form_id char(32), ".
      "fullname char(128), ".
      "company char(128), ".
      "email char(255), ".
      "dayphone char(14), ".
      "submit_ts bigint(20) NOT NULL default '0', ".
      "PRIMARY KEY (id)".
      ")";
   
?>