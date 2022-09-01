<?php

//---------------------------------------------------------------------------
// form definition : faqs
//---------------------------------------------------------------------------

   $FORM_FIELDS_ARRAY[0] = array(
      "TYPE" => "hidden",
      "NAME" => "faqid",
      "ID" => "faqid",
   );

   $FORM_FIELDS_ARRAY[1] = array(
      "TYPE" => "hidden",
      "NAME" => "faqlist",
      "ID" => "faqlist",
   );

   $FORM_FIELDS_ARRAY[2] = array(
      "TYPE" => "text",
      "NAME" => "title",
      "ID" => "title",
      "SIZE" => 50,
      "MAXLENGTH" => 255,
      "LABEL" => "<u>T</u>itle",
      "ACCESSKEY" => "T",
   );

   $FORM_FIELDS_ARRAY[3] = array(
      "TYPE" => "textarea",
      "NAME" => "shortquestion",
      "ID" => "shortquestion",
      "COLS" => 50,
      "ROWS" => 4,
      "LABEL" => "<u>S</u>hort Question",
      "ACCESSKEY" => "A",
   );

   $FORM_FIELDS_ARRAY[4] = array(
      "TYPE" => "textarea",
      "NAME" => "question",
      "ID" => "question",
      "COLS" => 50,
      "ROWS" => 6,
      "LABEL" => "<u>Q</u>uestion",
      "ACCESSKEY" => "Q",
   );

   $FORM_FIELDS_ARRAY[5] = array(
      "TYPE" => "textarea",
      "NAME" => "answer",
      "ID" => "answer",
      "COLS" => 50,
      "ROWS" => 12,
      "LABEL" => "<u>A</u>nswer",
      "ACCESSKEY" => "A",
   );

   $FORM_FIELDS_ARRAY[6] = array(
      "TYPE" => "checkbox",
      "NAME" => "flagasnew",
      "ID" => "flagasnew",
      "CHECKED" => "yes",
      "VALUE" => 1,
      "LABEL" => "<u>F</u>lag As New",
      "ACCESSKEY" => "F",
   );
   
//   $FORM_FIELDS_ARRAY[6] = array(
//      "TYPE" => "radio",
//      "NAME" => "flagasnew",
//      "VALUE" => 1,
//      "ID" => "yes_flagasnew",
//      "ValidateAsSet" => 1,
//      "ValidateAsSetErrorMessage" => "You must set the flag on or off",
//      "LABEL" => "Yes",
//      "readOnlyMark" => "[X]",
//   );

//   $FORM_FIELDS_ARRAY[7] = array(
//      "TYPE" => "radio",
//      "NAME" => "flagasnew",
//      "VALUE" => 0,
//      "ID" => "no_flagasnew",
//      "LABEL" => "No",
//      "readOnlyMark" => "[X]",
//   );
      
   $FORM_FIELDS_ARRAY[7] = array(
      "TYPE" => "checkbox",
      "NAME" => "status",
      "ID" => "status",
      "CHECKED" => "yes",
      "VALUE" => 1,
      "LABEL" => "<u>S</u>tatus",
      "ACCESSKEY" => "S",
   );
   
   $FORM_FIELDS_ARRAY[8] = array(
      "TYPE" => "hidden",
      "NAME" => "position",
      "ID" => "position",
   );

   $FORM_FIELDS_ARRAY[9] = array(
      "TYPE" => "hidden",
      "NAME" => "datecreated",
      "ID" => "datecreated",
   );

   $FORM_FIELDS_ARRAY[10] = array(
      "TYPE" => "hidden",
      "NAME" => "lastmodified",
      "ID" => "lastmodified",
   );
      
   $FORM_FIELDS_ARRAY[11] = array(
      "TYPE" => "submit",
      "ID" => "button_save",
      "VALUE" => "Save FAQ",
      "ACCESSKEY" => "v",
   );
   
   $FORM_FIELDS_ARRAY[12] = array(
      "TYPE" => "reset",
      "ID" => "button_reset",
      "VALUE" => "Reset",
      "ACCESSKEY" => "s",
   );
   
   $FORM_SAVE_ARRAY = array(
      "faqid" => "number",
      "faqlist" => "text",
      "title" => "text",
      "shortquestion" => "text",
      "question" => "text",
      "answer" => "text",
      "flagasnew" => "number",
      "status" => "number",
      "position" => "number",
      "datecreated" => "text",
      "lastmodified" => "text",
   );

   $FORM_PROCESS_ARRAY = array(
      "faqid" => "none",
      "faqlist" => "trim|htmlentities", // removed addslashes
      "title" => "trim|htmlentities", // removed addslashes
      "shortquestion" => "trim|htmlentities", // removed addslashes
      "question" => "trim|htmlentities", // removed addslashes
      "answer" => "trim|htmlentities", // removed addslashes
      "flagasnew" => "none",
      "status" => "none",
      "position" => "none",
      "datecreated" => "trim|htmlentities", // removed addslashes
      "lastmodified" => "trim|htmlentities", // removed addslashes
   );

   $FORM_UNPROCESS_ARRAY = array(
      "faqid" => "none",
      "faqlist" => "stripslashes|htmlentitydecode",
      "title" => "stripslashes|htmlentitydecode",
      "shortquestion" => "stripslashes|htmlentitydecode",
      "question" => "stripslashes|htmlentitydecode",
      "answer" => "stripslashes|htmlentitydecode",
      "flagasnew" => "none",
      "status" => "none",
      "position" => "none",
      "datecreated" => "stripslashes|htmlentitydecode",
      "lastmodified" => "stripslashes|htmlentitydecode",
   );

   $TABLE_CREATE_STATEMENT = "CREATE TABLE faqs (".
      "faqid int unsigned NOT NULL auto_increment PRIMARY KEY, ".
      "faqlist char(32) NOT NULL, ".
      "title char(255), ".
      "shortquestion text, ".
      "question text, ".
      "answer text, ".
      "flagasnew int, ".
      "status int NOT NULL, ".
      "position int, ".
      "datecreated char(30), ".
      "lastmodified char(30) ".
      ")";

   
?>