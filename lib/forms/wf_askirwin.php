<?php

//---------------------------------------------------------------------------
// form definition : askirwin
//---------------------------------------------------------------------------

   $FORM_FIELDS_ARRAY[0] = array(
      "TYPE" => "text",
      "NAME" => "fullname",
      "ID" => "fullname",
      "SIZE" => 30,
      "MAXLENGTH" => 40,
      "Capitalization" => "words",
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter your name.",
      "LABEL" => "Full Name",
   );

   $FORM_FIELDS_ARRAY[1] = array(
      "TYPE" => "text",
      "NAME" => "email",
      "ID" => "email",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a valid email address.",
      "ValidateAsEmail" => 1,
      "ValidateAsEmailErrorMessage" => "This does not appear to be a valid email.",
      "LABEL" => "Email Address",
   );

   $FORM_FIELDS_ARRAY[2] = array(
      "TYPE" => "text",
      "NAME" => "email2",
      "ID" => "email2",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsEqualTo" => "email",
      "ValidationErrorMessage" => "The two e-mail addresses are not the same.",
      "LABEL" => "Re-enter E-Mail Address",
   );

   $FORM_FIELDS_ARRAY[3] = array(
      "TYPE" => "textarea",
      "NAME" => "question",
      "ID" => "question",
      "COLS" => 50,
      "ROWS" => 15,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a message.",
      "LABEL" => "Message",
   );

   $FORM_FIELDS_ARRAY[4] = array(
      "TYPE" => "submit",
      "ID" => "button_send",
      "VALUE" => "Send Message",
      "ACCESSKEY" => "S",
   );
   
   $FORM_FIELDS_ARRAY[5] = array(
      "TYPE" => "reset",
      "ID" => "button_reset",
      "VALUE" => "Reset",
      "ACCESSKEY" => "R",
   );

   $FORM_SAVE_ARRAY = array(
      'fullname'     => "text",
      'email'        => "text",
      'question'      => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "CREATE TABLE wf_webmaster (".
      "id int(11) NOT NULL auto_increment, ".
      "form_id char(32), ".
      "fullname char(25), ".
      "email char(255), ".
      "question text, ".
      "submit_ts bigint(20) NOT NULL default '0', ".
      "PRIMARY KEY (id)".
      ")";
   
?>