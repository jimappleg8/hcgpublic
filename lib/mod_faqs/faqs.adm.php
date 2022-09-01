<?php

// =========================================================================
// faqs.adm.php
// written by Jim Applegate
//
// =========================================================================

require_once("dbi_adodb.inc.php");
require_once('formsprocessing.class.php');
require_once("formsgeneration.inc.php");
require_once("template.class.php");

if ( ! defined('DEBUG'))
{
   define("DEBUG", 0);
}


// ------------------------------------------------------------------------
// TAG: adm_manage_faqs
//   This is the controller for all administrative functions. It calls
//   other functions based on the $action supplied. The default action
//   is to display the list of faqs with links to modify them.
//
// ------------------------------------------------------------------------

function adm_manage_faqs($siteid, $faqlist, $action = "display", $faq_num = "", $lastaction="") 
{
   global $_HCG_GLOBAL;
   
   $db = HCGNewConnection("hcg_public_master");
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $display_list = true;
   $result = 0;
   
   if ($action == "display") {
      $result = 1;
   } elseif ($action == "toggle") {
      $result = adm_change_faq_status($db, $faq_num, $lastaction);
   } elseif ($action == "newflag") {
      $result = adm_change_faq_newflag($db, $faq_num, $lastaction);
   } elseif ($action == "moveup") {
      $result = adm_change_faq_position($db, $faq_num, "up", $lastaction);
   } elseif ($action == "movedn") {
      $result = adm_change_faq_position($db, $faq_num, "dn", $lastaction);
   } elseif ($action == "delete") {
      $result = adm_trash_faq($db, $faq_num, $lastaction);
   } elseif ($action == "create") {
      $result = adm_create_faq($db, $siteid, $faqlist);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   } elseif ($action == "edit") {
      $result = adm_edit_faq($db, $siteid, $faq_num);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   }
   
   if ($result != 1) {
      $faqs['error_msg'] = $result;
   }
   
   if ($display_list == true) {
   
      $faqs['siteid'] = $siteid;
      $faqs['faqlist'] = $faqlist;

      $query = "SELECT * FROM faqs " .
               "WHERE faqlist LIKE \"".$faqlist."\" " .
               "AND status <= 1 ".
               "ORDER BY position";
      
      $faq_list = $db->GetAll($query);
      $num_faqs = count($faq_list);
      if ($num_faqs == 0) {
         $faqs['faq_exists'] = false;
      } else {
         $faqs['faq_exists'] = true;
         $faqs['num_faqs'] = $num_faqs;
      }
      // new
      $settings = adm_get_faq_settings();
      $settings['form_template'] = "faqs_adm_managefaqs.tpl"; // probably unnecessary
      $s = new forms_processing($db);
      $s->setConfigs($settings);
      for ($i=0; $i<$num_faqs; $i++) {
         $s->setFields($faq_list[$i]);
         $s->unprocessData();
         $faq_list[$i] = $s->getFields(); // this method doesn't exist yet
      }
      // end new
      $t = new HCG_Smarty;

      $t->assign("faqs", $faqs);
      $t->assign("faq_list", $faq_list);
      $t->assign("lastaction", $_SESSION['user_last_action'] + 1);
      
      $t->setTplPath("faqs_adm_managefaqs.tpl");
      echo $t->fetch("faqs_adm_managefaqs.tpl");
   }
}


// ------------------------------------------------------------------------
// adm_change_faq_status
//
// ------------------------------------------------------------------------

function adm_change_faq_status(&$dbi, $faq_num, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $query1 = "SELECT status FROM faqs " .
                "WHERE faqid = ".$faq_num;
   
      $row = $dbi->GetRow($query1);
   
      if ($row['status'] == 1) {
         $new_status = 0;
      } elseif ($row['status'] == 0) {
         $new_status = 1;
      }
      
      $query2 = "UPDATE faqs " . 
                "SET status = ".$new_status." " .
                "WHERE faqid = ".$faq_num;

      $dbi->Execute($query2);
   }

   return 1;
}


// ------------------------------------------------------------------------
// adm_change_faq_newflag
//
// ------------------------------------------------------------------------

function adm_change_faq_newflag(&$dbi, $faq_num, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $query1 = "SELECT flagasnew FROM faqs " .
                "WHERE faqid = ".$faq_num;
   
      $row = $dbi->GetRow($query1);
   
      if ($row['flagasnew'] == 1) {
         $new_flagasnew = 0;
      } elseif ($row['flagasnew'] == 0) {
         $new_flagasnew = 1;
      }
      
      $query2 = "UPDATE faqs " . 
                "SET flagasnew = ".$new_flagasnew." " .
                "WHERE faqid = ".$faq_num;

      $dbi->Execute($query2);
   }

   return 1;
}


// ------------------------------------------------------------------------
// adm_change_faq_position
//
// ------------------------------------------------------------------------

function adm_change_faq_position(&$dbi, $faq_num, $direction, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $query1 = "SELECT position, faqlist FROM faqs " .
                "WHERE faqid = ".$faq_num;
   
      $row = $dbi->GetRow($query1);
      
      if ($direction == "dn") {
          $query2 = "UPDATE faqs ".
                    "SET position = ".$row['position']." ".
                    "WHERE position = ".($row['position']+1)." ".
                    "AND faqlist LIKE '".$row['faqlist']."'";
          $dbi->Execute($query2);
          $query3 = "UPDATE faqs ".
                    "SET position = ".($row['position']+1)." ".
                    "WHERE faqid = ".$faq_num;
          $dbi->Execute($query3);
      } else {
          $query2 = "UPDATE faqs ".
                    "SET position = ".$row['position']." ".
                    "WHERE position = ".($row['position']-1)." ".
                    "AND faqlist LIKE '".$row['faqlist']."'";
          $dbi->Execute($query2);
          $query3 = "UPDATE faqs ".
                    "SET position = ".($row['position']-1)." ".
                    "WHERE faqid = ".$faq_num;
          $dbi->Execute($query3);
      }
   }

   return 1;
}


// ------------------------------------------------------------------------
// adm_trash_faq
//
// ------------------------------------------------------------------------

function adm_trash_faq(&$dbi, $faq_num, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $query1 = "SELECT position, faqlist FROM faqs " .
                "WHERE faqid = ".$faq_num;
   
      $row = $dbi->GetRow($query1);
      
      $query2 = "UPDATE faqs ".
                "SET status = 2, position = 0 ".
                "WHERE faqid = ".$faq_num;

      $dbi->Execute($query2);
      
      for ($i=($row['position']+1); $i<get_next_faq_position($dbi, $row['faqlist']); $i++) {
         $query3 = "UPDATE faqs ".
                   "SET position =".($i-1)." ".
                   "WHERE position = ".$i." ".
                   "AND faqlist LIKE '".$row['faqlist']."'";
         $dbi->Execute($query3);
      }
   }   
   return 1;
}


// ------------------------------------------------------------------------
// adm_get_faq_settings
//
// ------------------------------------------------------------------------

function adm_get_faq_settings()
{
   $settings['form_id'] = "faqs";
//   $settings['form_name'] = "";
   $settings['form_database'] = "hcg_public_master";
   $settings['form_table'] = "faqs";
//   $settings['acl_allow_from'] = "";
//   $settings['acl_deny_from'] = "";
//   $settings['form_log_file'] = "";
   $settings['form_def_file'] = "faqs.php";
   $settings['upload_file'] = false;
   $settings['upload_file_dir'] = "";
   $settings['upload_file_fields'] = array();
//   $settings['form_template'] = "";
//   $settings['send_outbound_mail'] = true;
//   $settings['outbound_mail_template'] = "";
//   $settings['send_inbound_mail'] = true;
//   $settings['inbound_mail_template'] = "";
//   $settings['show_thankyou_template'] = "";
   $settings['errors'] = array();
   return $settings;
}


// ------------------------------------------------------------------------
// adm_create_faq
//
// ------------------------------------------------------------------------

function adm_create_faq(&$dbi, $site_id, $faqlist) 
{
   global $_HCG_GLOBAL;
   
   $display_form = true;
   
   $settings = adm_get_faq_settings();
   $settings['form_name'] = "createfaq";
   $settings['form_template'] = "faqs_adm_createfaq.tpl";
   
   $f = new form_class();
   
   $f->NAME = $settings['form_name'];
   $f->METHOD = "POST";
   $f->ACTION = $HTTP_SERVER_VARS["PHP-SELF"];
   $f->debug = "OutputDebug";
   $f->ResubmitConfirmMessage = "You have already submitted this form and it has been received. Thank you.";

   include($_HCG_GLOBAL['forms_dir']."/".$settings['form_def_file']);

   for ($i=0; $i<count($FORM_FIELDS_ARRAY); $i++) {
      $f->AddInput($FORM_FIELDS_ARRAY[$i]);
   }
   
   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "doit",
      "VALUE" => 1,
   ));
   
   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "user_track",
      "VALUE" => 134764475,
   ));

   $f->LoadInputValues($f->WasSubmitted("doit"));
   
   $verify = array();
   
   // PART 1: Check if the form was submitted/correctly filled out

   if ($f->WasSubmitted("doit")) {
   
      if ($f->GetInputValue("user_track") <= $_SESSION['user_last_action']) {
         $process_form = false;
         $display_form = false;
      } else {
         if (($error_message = $f->Validate($verify)) == "") {
            $process_form = true;
         } else {
            $process_form = false;
            foreach ($error_message as $field => $errormsg) {
               $errors[$field] = htmlentities($errormsg);
            }
         }
      }
   
   } else {
   
      $process_form = false;
      $f->SetInputValue("user_track", $_SESSION['user_last_action']+1);
   
   }
   
   // PART 2: Save the data if applicable

   if ($process_form == true) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;

      $s = new forms_processing($dbi);
      $s->setConfigs($settings);
      
      $s->importFields($f);
      $s->unsetField("faqid"); // so it can be auto-incremented
//      $s->setField("datecreated", date("F j, Y - g:i a"));
//      $s->setField("lastmodified", date("F j, Y - g:i a"));
      
      $s->processData();
      $result = $s->insertData();
      
      if ($result == false) {
         $faqs['error_msg'] = "There was an error saving this record: ".$dbi->ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
      } else {
         $result = 1;
         move_last_faq_first($dbi, $faqlist);
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      if (DEBUG) echo "entered the display area.";
   
      // A. set the form variables

      $f->SetInputValue("faqid", 0);
      $f->SetInputValue("faqlist", $faqlist);
      $f->SetInputValue("position", get_next_faq_position($dbi, $faqlist));
      $f->SetInputValue("datecreated", date("F j, Y - g:i a"));
      $f->SetInputValue("lastmodified", date("F j, Y - g:i a"));

      // B. set other template variables
      
      $faqs['datecreated'] = $f->GetInputValue("datecreated");
      $faqs['lastmodified'] = $f->GetInputValue("lastmodified");
      $faqs['siteid'] = $site_id;
      $faqs['faqlist'] = $faqlist;

      $t = new HCG_Smarty;
      $t->assign("faqs", $faqs);

      $t->assign_by_ref("form", $f);
      $t->assign_by_ref("verify", $verify);
      $t->assign("errors", $errors);
      $t->register_prefilter("smarty_prefilter_form");

      $t->setTplPath("faqs_adm_createfaq.tpl");
      $t->fetch("faqs_adm_createfaq.tpl");
      $t->unregister_prefilter("smarty_prefilter_form");
      echo FormCaptureOutput($f, array("EndOfLine"=>"\n"));
      $result = "in_progress";
   }
   return $result;
}


// ------------------------------------------------------------------------
// adm_edit_faq
//
// ------------------------------------------------------------------------

function adm_edit_faq(&$dbi, $site_id, $faq_num) 
{
   global $_HCG_GLOBAL;
   
   $display_form = true;
   $errors_found = false;
   
   $settings = adm_get_faq_settings();
   $settings['form_name'] = "editfaq";
   $settings['form_template'] = "faqs_adm_editfaq.tpl";
   
   $f = new form_class();
   
   $s = new forms_processing($dbi);
   $s->setConfigs($settings);
   
   $f->NAME = $settings['form_name'];
   $f->METHOD = "POST";
   $f->ACTION = $HTTP_SERVER_VARS["PHP-SELF"];
   $f->debug = "OutputDebug";
   $f->ResubmitConfirmMessage = "You have already submitted this form and it has been received. Thank you.";

   include($_HCG_GLOBAL['forms_dir']."/".$settings['form_def_file']);

   for ($i=0; $i<count($FORM_FIELDS_ARRAY); $i++) {
      $f->AddInput($FORM_FIELDS_ARRAY[$i]);
   }
   
   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "doit",
      "VALUE" => 1,
   ));
   
   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "user_track",
      "VALUE" => 134764475,
   ));

   $f->LoadInputValues($f->WasSubmitted("doit"));
   
   $verify = array();
   
   // PART 1: Check if the form was submitted/correctly filled out

   if ($f->WasSubmitted("doit")) {
   
      if ($f->GetInputValue("user_track") <= $_SESSION['user_last_action']) {
         $process_form = false;
         $display_form = false;
      } else {
         if (($error_message = $f->Validate($verify)) == "") {
            $process_form = true;
         } else {
            $process_form = false;
            $errors_found = true;
            foreach ($error_message as $field => $errormsg) {
               $errors[$field] = htmlentities($errormsg);
            }
         }
      }
   
   } else {
   
      $process_form = false;
      $f->SetInputValue("user_track", $_SESSION['user_last_action']+1);
   
   }
   
   // PART 2: Save the data if applicable

   if ($process_form == true) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;
      
      $s->importFields($f);
      $s->unsetField("faqid"); // so it can be specified in update
      $s->setField("lastmodified", date("F j, Y - g:i a"));
      
      $s->processData();
      $result = $s->updateData("faqid", $faq_num);
      
      if ($result == false) {
         $faqs['error_msg'] = "There was an error saving this record: ".$dbi->ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
         $errors_found = true;
      } else {
         $result = 1;
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      if (DEBUG) echo "entered the display area.";
   
      // A. set the form variables
      
      if ($errors_found == false) {
         $s->selectData(array(faqid => $faq_num));
         //$s->setField("lastmodified", date("F j, Y - g:i a"));
         $s->unprocessData();
         $s->exportFields($f);
      }

      // B. set other template variables
      
      $faqs['datecreated'] = $f->GetInputValue("datecreated");
      $faqs['lastmodified'] = $f->GetInputValue("lastmodified");
      $faqs['siteid'] = $site_id;
      $faqs['faqlist'] = $faqlist;

      $t = new HCG_Smarty;
      $t->assign("faqs", $faqs);

      $t->assign_by_ref("form", $f);
      $t->assign_by_ref("verify", $verify);
      $t->assign("errors", $errors);
      $t->register_prefilter("smarty_prefilter_form");

      $t->setTplPath("faqs_adm_editfaq.tpl");
      $t->fetch("faqs_adm_editfaq.tpl");
      $t->unregister_prefilter("smarty_prefilter_form");
      echo FormCaptureOutput($f, array("EndOfLine"=>"\n"));
      $result = "in_progress";
   }
   return $result;
}


// ------------------------------------------------------------------------
// get_next_faq_position
//
// ------------------------------------------------------------------------
function get_next_faq_position(&$dbi, $faqlist)
{
   $query1 = "SELECT position FROM faqs " .
             "WHERE faqlist LIKE '".$faqlist."'";
   $results = $dbi->GetAll($query1);

   return (count($results) + 1);
}


// ------------------------------------------------------------------------
// move_last_faq_first
//
// ------------------------------------------------------------------------
function move_last_faq_first(&$dbi, $faqlist)
{
   $query1 = "SELECT position FROM faqs " .
             "WHERE faqlist LIKE '".$faqlist."'";
   $results = $dbi->GetAll($query1);

   $list_size = count($results);
   for ($i=$list_size; $i>=1; $i--) {
         $query2 = "UPDATE faqs ".
                   "SET position = ".($i+1)." ".
                   "WHERE position = ".$i." ".
                   "AND faqlist LIKE '".$faqlist."'";
      $dbi->Execute($query2);
   }

   $query3 = "UPDATE faqs ".
             "SET position = 1 ".
             "WHERE position = ".($list_size+1)." ".
             "AND faqlist LIKE '".$faqlist."'";
   $dbi->Execute($query3);

   return 1;
}


?>
