<?php

//---------------------------------------------------------------------------
// form definition : donations
//---------------------------------------------------------------------------

   $FORM_FIELDS_ARRAY[0] = array(
      "TYPE" => "text",
      "NAME" => "app_date",
      "ID" => "app_date",
      "SIZE" => 20,
      "MAXLENGTH" => 50,
      "LABEL" => "Date of Application",
   );

   $FORM_FIELDS_ARRAY[1] = array(
      "TYPE" => "text",
      "NAME" => "org_name",
      "ID" => "org_name",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter your organization's name.",
      "LABEL" => "Organization Name",
   );

   $FORM_FIELDS_ARRAY[2] = array(
      "TYPE" => "textarea",
      "NAME" => "address",
      "ID" => "address",
      "COLS" => 30,
      "ROWS" => 3,
      "LABEL" => "Address<br>City, State, Zip",
   );

   $FORM_FIELDS_ARRAY[3] = array(
      "TYPE" => "text",
      "NAME" => "con_name",
      "ID" => "con_name",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a contact's name.",
      "LABEL" => "Contact Name",
   );

   $FORM_FIELDS_ARRAY[4] = array(
      "TYPE" => "text",
      "NAME" => "con_phone",
      "ID" => "con_phone",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "ValidateAsNotEmpty" => 1,
      "ValidationErrorMessage" => "You must enter a contact's phone.",
      "LABEL" => "Contact Phone",
   );

   $FORM_FIELDS_ARRAY[5] = array(
      "TYPE" => "text",
      "NAME" => "con_fax",
      "ID" => "con_fax",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "LABEL" => "Contact Fax",
   );

   $FORM_FIELDS_ARRAY[6] = array(
      "TYPE" => "text",
      "NAME" => "con_email",
      "ID" => "con_email",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsNotEmpty" => 1,
      "ValidateAsNotEmptyErrorMessage" => "You must enter a valid email address.",
      "ValidateAsEmail" => 1,
      "ValidateAsEmailErrorMessage" => "This does not appear to be a valid email.",
      "LABEL" => "Contact E-Mail",
   );

   $FORM_FIELDS_ARRAY[7] = array(
      "TYPE" => "text",
      "NAME" => "email2",
      "ID" => "email2",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "Capitalization" => "lowercase",
      "ValidateAsEqualTo" => "con_email",
      "ValidationErrorMessage" => "The two e-mail addresses are not the same.",
      "LABEL" => "Please Confirm E-Mail",
   );

   $FORM_FIELDS_ARRAY[8] = array(
      "TYPE" => "text",
      "NAME" => "event_date",
      "ID" => "event_date",
      "SIZE" => 30,
      "MAXLENGTH" => 50,
      "LABEL" => "DATE(S) OF EVENT",
   );

   $FORM_FIELDS_ARRAY[9] = array(
      "TYPE" => "text",
      "NAME" => "request_type",
      "ID" => "request_type",
      "SIZE" => 30,
      "LABEL" => "Type of Donation Request (money, tea samplers, tea boxes, gift baskets, etc.)",
   );

   $FORM_FIELDS_ARRAY[10] = array(
      "TYPE" => "text",
      "NAME" => "attendance",
      "ID" => "attendance",
      "SIZE" => 30,
      "MAXLENGTH" => 10,
      "LABEL" => "Amount/Number<br>/Attendance",
   );

   $FORM_FIELDS_ARRAY[11] = array(
      "TYPE" => "textarea",
      "NAME" => "org_desc",
      "ID" => "org_desc",
      "COLS" => 30,
      "ROWS" => 3,
      "LABEL" => "Short Description<br>of Organization",
   );

   $FORM_FIELDS_ARRAY[12] = array(
      "TYPE" => "radio",
      "NAME" => "org_status",
      "ID" => "non_profit_status",
      "VALUE" => "non-profit",
      "LABEL" => "Non-profit",
      "readOnlyMark" => "[X]",
   );

   $FORM_FIELDS_ARRAY[13] = array(
      "TYPE" => "radio",
      "NAME" => "org_status",
      "ID" => "for_profit_status",
      "VALUE" => "for profit/other",
      "LABEL" => "For Profit/Other",
      "readOnlyMark" => "[X]",
   );

   $FORM_FIELDS_ARRAY[14] = array(
      "TYPE" => "text",
      "NAME" => "501c3_num",
      "ID" => "501c3_num",
      "SIZE" => 30,
      "MAXLENGTH" => 30,
      "LABEL" => "501(C)3 Number",
   );

   $FORM_FIELDS_ARRAY[15] = array(
      "TYPE" => "text",
      "NAME" => "fed_id_num",
      "ID" => "fed_id_num",
      "SIZE" => 30,
      "MAXLENGTH" => 30,
      "LABEL" => "Fed Id No.",
   );

   $FORM_FIELDS_ARRAY[16] = array(
      "TYPE" => "textarea",
      "NAME" => "event_desc",
      "ID" => "event_desc",
      "COLS" => 30,
      "ROWS" => 3,
      "LABEL" => "Short Description<br>of Event/Need",
   );

   $FORM_FIELDS_ARRAY[17] = array(
      "TYPE" => "textarea",
      "NAME" => "mail_address",
      "ID" => "mail_address",
      "COLS" => 30,
      "ROWS" => 3,
      "LABEL" => "Mailing Address<br>for Product Delivery<br>if different than above",
   );

   $FORM_FIELDS_ARRAY[18] = array(
      "TYPE" => "text",
      "NAME" => "signature",
      "ID" => "signature",
      "SIZE" => 30,
      "MAXLENGTH" => 255,
      "LABEL" => "\"Signature\" of Representative<br>Enter Name",
   );

   $FORM_FIELDS_ARRAY[19] = array(
      "TYPE" => "submit",
      "ID" => "button_register",
      "VALUE" => "Submit Request",
      "ACCESSKEY" => "S",
   );
   
   $FORM_FIELDS_ARRAY[20] = array(
      "TYPE" => "reset",
      "ID" => "button_reset",
      "VALUE" => "Reset",
      "ACCESSKEY" => "R",
   );
   
   $FORM_SAVE_ARRAY = array(
      "app_date"      => "text",
      "org_name"      => "text",
      "address"       => "text",
      "con_name"      => "text",
      "con_phone"     => "text",
      "con_fax"       => "text",
      "con_email"     => "text",
      "event_date"    => "text",
      "request_type"  => "text",
      "attendance"    => "text",
      "org_desc"      => "text",
      "org_status"    => "text",
      "501c3_num"     => "text",
      "fed_id_num"    => "text",
      "event_desc"    => "text",
      "mail_address"  => "text",
      "signature"     => "text",
    );

   $TABLE_CREATE_STATEMENT = "CREATE TABLE wf_donations (".
      "id int(11) NOT NULL auto_increment, ".
      "form_id char(32), ".
      "app_date char(50), ".
      "org_name char(255), ".
      "address text, ".
      "con_name char(255), ".
      "con_phone char(20), ".
      "con_fax char(20), ".
      "con_email char(255), ".
      "event_date char(50), ".
      "request_type text, ".
      "attendance char(10), ".
      "org_desc text, ".
      "org_status char(16), ".
      "501c3_num char(30), ".
      "fed_id_num char(30), ".
      "event_desc text, ".
      "mail_address text, ".
      "signature char(255), ".
      "submit_ts bigint(20) NOT NULL default '0', ".
      "PRIMARY KEY (id)".
      ")";  
?>