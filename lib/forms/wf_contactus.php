<?php

//---------------------------------------------------------------------------
// form definition : contactus
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
      "NAME" => "phone",
      "ID" => "phone",
      "SIZE" => 25,
      "MAXLENGTH" => 12,
      "LABEL" => "Daytime Phone",
   );

   $FORM_FIELDS_ARRAY[8] = array(
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

   $FORM_FIELDS_ARRAY[9] = array(
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

   $FORM_FIELDS_ARRAY[10] = array(
      "TYPE" => "textarea",
      "NAME" => "comment",
      "ID" => "comment",
      "COLS" => 50,
      "ROWS" => 15,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a message.",
      "LABEL" => "Message",
   );

   $FORM_FIELDS_ARRAY[11] = array(
      "TYPE" => "hidden",
      "NAME" => "favorites",
      "ID" => "favorites",
   );

   $FORM_FIELDS_ARRAY[12] = array(
      "TYPE" => "hidden",
      "NAME" => "site",
      "ID" => "site",
   );

   $FORM_FIELDS_ARRAY[13] = array(
      "TYPE" => "hidden",
      "NAME" => "child1",
      "ID" => "child1",
   );

   $FORM_FIELDS_ARRAY[14] = array(
      "TYPE" => "hidden",
      "NAME" => "childdob1",
      "ID" => "childdob1",
   );

   $FORM_FIELDS_ARRAY[15] = array(
      "TYPE" => "hidden",
      "NAME" => "childgender1",
      "ID" => "childgender1",
   );

   $FORM_FIELDS_ARRAY[16] = array(
      "TYPE" => "hidden",
      "NAME" => "child2",
      "ID" => "child2",
   );

   $FORM_FIELDS_ARRAY[17] = array(
      "TYPE" => "hidden",
      "NAME" => "childdob2",
      "ID" => "childdob2",
   );

   $FORM_FIELDS_ARRAY[18] = array(
      "TYPE" => "hidden",
      "NAME" => "childgender2",
      "ID" => "childgender2",
   );

   $FORM_FIELDS_ARRAY[19] = array(
      "TYPE" => "hidden",
      "NAME" => "child3",
      "ID" => "child3",
   );

   $FORM_FIELDS_ARRAY[20] = array(
      "TYPE" => "hidden",
      "NAME" => "childdob3",
      "ID" => "childdob3",
   );

   $FORM_FIELDS_ARRAY[21] = array(
      "TYPE" => "hidden",
      "NAME" => "childgender3",
      "ID" => "childgender3",
   );

   $FORM_FIELDS_ARRAY[22] = array(
      "TYPE" => "checkbox",
      "NAME" => "marketing",
      "ID" => "marketing",
      "CHECKED" => "yes",
      "VALUE" => "YES",
      "LABEL" => "Would you like to receive information from us in the future? If yes, leave this box checked.",
   );

   $FORM_FIELDS_ARRAY[23] = array(
      "TYPE" => "checkbox",
      "NAME" => "release",
      "ID" => "release",
      "CHECKED" => "yes",
      "VALUE" => "YES",
      "LABEL" => "From time to time, we select consumer comments to post on our web site. Please check this box if you would like your comments to be considered.",
   );

   $FORM_FIELDS_ARRAY[24] = array(
      "TYPE" => "submit",
      "ID" => "button_send",
      "VALUE" => "Send Message",
      "ACCESSKEY" => "S",
   );
   
   $FORM_FIELDS_ARRAY[25] = array(
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
      'phone'        => "text",
      'email'        => "text",
      'comment'      => "text",
      'favorites'    => "text",
      'site'         => "text",
      'child1'       => "text",
      'childdob1'    => "text",
      'childgender1' => "text",
      'child2'       => "text",
      'childdob2'    => "text",
      'childgender2' => "text",
      'child3'       => "text",
      'childdob3'    => "text",
      'childgender3' => "text",
      'marketing'    => "text",
      'release'      => "text",
   );
   
   $TABLE_CREATE_STATEMENT = "CREATE TABLE wf_contactus (".
      "id int(11) NOT NULL auto_increment, ".
      "form_id char(32), ".
      "fname char(25), ".
      "lname char(25), ".
      "address1 char(40), ".
      "address2 char(40), ".
      "city char(30), ".
      "state char(2), ".
      "zip char(10), ".
      "phone char(14), ".
      "email char(255), ".
      "comment text, ".
      "favorites text, ".
      "site char(10), ".
      "child1 char(40), ".
      "childdob1 char(8), ".
      "childgender1 char(1), ".
      "child2 char(40), ".
      "childdob2 char(8), ".
      "childgender2 char(1), ".
      "child3 char(40), ".
      "childdob3 char(8), ".
      "childgender3 char(1), ".
      "marketing char(3), ".
      "release char(3), ".
      "submit_ts bigint(20) NOT NULL default '0', ".
      "PRIMARY KEY (id)".
      ")";
   
?>