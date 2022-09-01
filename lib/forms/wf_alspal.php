<?php

//---------------------------------------------------------------------------
// form definition : alspal (Al's Pal on Rosetto site)
//---------------------------------------------------------------------------

   $FORM_FIELDS_ARRAY[0] = array(
      "TYPE" => "text",
      "NAME" => "fname",
      "ID" => "fname",
      "SIZE" => 20,
      "MAXLENGTH" => 25,
      "Capitalization" => "words",
      "LABEL" => "First Name",
   );

   $FORM_FIELDS_ARRAY[1] = array(
      "TYPE" => "text",
      "NAME" => "lname",
      "ID" => "lname",
      "SIZE" => 20,
      "Capitalization" => "words",
      "MAXLENGTH" => 25,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter your last name.",
      "LABEL" => "Last Name",
   );

   $FORM_FIELDS_ARRAY[2] = array(
      "TYPE" => "text",
      "NAME" => "address1",
      "ID" => "address1",
      "SIZE" => 30,
      "MAXLENGTH" => 40,
      "LABEL" => "Address",
   );

   $FORM_FIELDS_ARRAY[3] = array(
      "TYPE" => "text",
      "NAME" => "address2",
      "ID" => "address2",
      "SIZE" => 30,
      "MAXLENGTH" => 40,
      "LABEL" => "Address (line 2)",
   );

   $FORM_FIELDS_ARRAY[4] = array(
      "TYPE" => "text",
      "NAME" => "city",
      "ID" => "city",
      "SIZE" => 30,
      "MAXLENGTH" => 30,
      "LABEL" => "City",
   );

   $FORM_FIELDS_ARRAY[5] = array(
      "TYPE" => "text",
      "NAME" => "state",
      "ID" => "state",
      "SIZE" => 4,
      "MAXLENGTH" => 2,
      "Capitalization" => "uppercase",
      "LABEL" => "State",
   );

   $FORM_FIELDS_ARRAY[6] = array(
      "TYPE" => "text",
      "NAME" => "zip",
      "ID" => "zip",
      "SIZE" => 15,
      "MAXLENGTH" => 10,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a zip code.",
      "LABEL" => "Postal Code",
   );

   $FORM_FIELDS_ARRAY[7] = array(
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

   $FORM_FIELDS_ARRAY[8] = array(
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

   $FORM_FIELDS_ARRAY[9] = array(
      "TYPE" => "textarea",
      "NAME" => "comment",
      "ID" => "comment",
      "COLS" => 50,
      "ROWS" => 15,
      "LABEL" => "Comments",
   );

   $FORM_FIELDS_ARRAY[10] = array(
      "TYPE" => "submit",
      "ID" => "button_send",
      "VALUE" => "Send Message",
      "ACCESSKEY" => "S",
   );
   
   $FORM_FIELDS_ARRAY[11] = array(
      "TYPE" => "reset",
      "ID" => "button_reset",
      "VALUE" => "Reset",
      "ACCESSKEY" => "R",
   );

   $FORM_SAVE_ARRAY = array(
      'fname'        => "text",
      'lname'        => "text",
      'address1'     => "text",
      'address2'     => "text",
      'city'         => "text",
      'state'        => "text",
      'zip'          => "text",
      'email'        => "text",
      'comment'      => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "CREATE TABLE wf_alspal (".
      "id int(11) NOT NULL auto_increment, ".
      "form_id char(32), ".
      "fname char(25), ".
      "lname char(25), ".
      "address1 char(40), ".
      "address2 char(40), ".
      "city char(30), ".
      "state char(2), ".
      "zip char(10), ".
      "email char(255), ".
      "comment text, ".
      "submit_ts bigint(20) NOT NULL default '0', ".
      "PRIMARY KEY (id)".
      ")";
   
?>