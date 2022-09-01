<?php

// =========================================================================
// jobs.adm.php
// written by Jim Applegate
//
// =========================================================================


require_once("template.class.php");
require_once("mod_db/db.fns.php");


// ------------------------------------------------------------------------
// TAG: adm_manage_jobs
//   This is the controller for all administrative functions. It calls
//   other functions based on the $action supplied. The default action
//   is to display the list of jobs with links to modify them.
//
// ------------------------------------------------------------------------

function adm_manage_jobs($site_id, $action = "display", $job_num = "", $lastaction="") 
{
   global $_HCG_GLOBAL;
   
   reset_forms();
   
   $display_list = true;
   
   if ($action == "display") {
      $result = 1;
   } elseif ($action == "toggle") {
      $result = adm_change_job_status($job_num, $lastaction);
   } elseif ($action == "delete") {
      $result = adm_trash_job($job_num);
   } elseif ($action == "create") {
      $result = adm_create_job($site_id);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   } elseif ($action == "edit") {
      $result = adm_edit_job($job_num);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   }
   
   if ($result != 1) {
      $jobs['error_msg'] = $result;
   }
   
   if ($display_list == true) {
   
      $jobs['siteid'] = $site_id;

      $query = "SELECT * FROM jobs " .
               "WHERE siteid LIKE \"".$site_id."\" " .
               "AND status <= 1";

      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      
      $job_list = db_GetAll($query);
      $num_jobs = count($job_list);
      if ($num_jobs == 0) {
         $jobs['job_exists'] = false;
      } else {
         $jobs['job_exists'] = true;
         $query = "SELECT * FROM jobs_category " .
                  "WHERE siteid LIKE \"".$site_id."\" " .
                  "AND status <= 1";
         $cat_list = db_GetAll($query);
         // restructure the data for easier reference
         for ($i=0; $i<count($cat_list); $i++) {
            $category_list[$cat_list[$i]['categoryid']] = $cat_list[$i]['categoryname'];
         }
         // assign names to the $jobs data
         for ($i=0; $i<$num_jobs; $i++) {
            $job_list[$i]['category'] = $category_list[($job_list[$i]['categoryid'])];
         }
      }
      
      $t = new HCG_Smarty;

      $t->assign("jobs", $jobs);
      $t->assign("job_list", $job_list);
      $t->assign("lastaction", $_SESSION['user_last_action'] + 1);
	
      $t->setTplPath("jobs_adm_managejobs.tpl");
      echo $t->fetch("jobs_adm_managejobs.tpl");
   }
}


// ------------------------------------------------------------------------
// TAG: adm_manage_categories
//
// ------------------------------------------------------------------------

function adm_manage_categories($site_id, $action = "display", $cat_num = "", $lastaction="") 
{
   global $_HCG_GLOBAL;
   
   reset_forms();
   
   $display_list = true;

   if ($action == "display") {
      $result = 1;
   } elseif ($action == "toggle") {
      $result = adm_change_cat_status($cat_num, $lastaction);
   } elseif ($action == "delete") {
      $result = adm_trash_cat($cat_num);
   } elseif ($action == "create") {
      $result = adm_create_cat($site_id);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   } elseif ($action == "edit") {
      $result = adm_edit_cat($site_id, $cat_num);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   }
   
   if ($result != 1) {
      $category['error_msg'] = $result;
   }
   
   if ($display_list == true) {

      $category['siteid'] = $site_id;

      $query = "SELECT * FROM jobs_category " .
               "WHERE siteid LIKE \"".$site_id."\" " .
               "AND status <= 1";
   
      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      
      $category_list = db_GetAll($query);
      $num_cats = count($category_list);
      if ($num_cats == 0) {
         $category['category_exists'] = false;
      } else {
         $category['category_exists'] = true;
      }
      
      $t = new HCG_Smarty;

      $t->assign("category", $category);
      $t->assign("category_list", $category_list);
      $t->assign("lastaction", $_SESSION['user_last_action'] + 1);
	
      $t->setTplPath("jobs_adm_managecategories.tpl");
      echo $t->fetch("jobs_adm_managecategories.tpl");   
   }
}


// ############################ JOB FUNCTIONS #############################

// ------------------------------------------------------------------------
// adm_change_job_status
//
// ------------------------------------------------------------------------

function adm_change_job_status($job_num, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $query1 = "SELECT status FROM jobs " .
                "WHERE JobID = $job_num";
   
      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      $row = db_GetRow($query1);
   
      if ($row['status'] == 1) {
         $new_status = 0;
      } elseif ($row['status'] == 0) {
         $new_status = 1;
      }
      
      $query2 = "UPDATE jobs " . 
                "SET status = ".$new_status." " .
                "WHERE JobID = ".$job_num;

      db_Execute($query2);
   }

   return 1;
}


// ------------------------------------------------------------------------
// adm_trash_job
//
// ------------------------------------------------------------------------

function adm_trash_job($job_num) 
{
   $query = "UPDATE jobs " . 
            "SET status = 2 " .
            "WHERE JobID = ".$job_num;

   db_Connect('hcg_public_master');

   db_Execute($query);
   
   return 1;
}


// ------------------------------------------------------------------------
// adm_create_job
//
// ------------------------------------------------------------------------

function adm_create_job($site_id) 
{
   global $_HCG_GLOBAL;
   
   $passed_vars = $_HCG_GLOBAL['passed_vars'];
   unset($passed_vars['action']);
   
   $display_form = true;
   $save_data = false;
   
   // create template object for form
   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   // create formsess object to manipulate form variables
   $f = new Formsess('createjob');
   
   // PART 1: Check if the form was submitted/correctly filled out
   
   if (!empty($passed_vars)) {
   
      extract($passed_vars, EXTR_OVERWRITE);

      // detect if the form has already been saved once
      if ($lastaction > $_SESSION['user_last_action']) {
         $already_saved = false;
      } else {
         $already_saved = true;
      }
      
      $f->reset("errors");
      $f->performCheck();
      if ($f->hasErrors() == false) {
         $display_form = false;
         $save_data = true;
      }
   }   
   
   // PART 2: Save the data if applicable

   if (($save_data == true) && ($already_saved == false)) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;
      
      // process field contents
      $title = process_jobs_field($title);
      $category = process_jobs_field($category);
      $location = process_jobs_field($location);
      $summary = process_jobs_field($summary, true);
      $description = process_jobs_field($description, true);
      $status = process_jobs_field($status);
      $datecreated = process_jobs_field($datecreated);
      $lastmodified = process_jobs_field($lastmodified);
      
      $query = "INSERT INTO jobs (jobid, siteid, title, categoryid, location, summary, description, status, datecreated, lastmodified)" .
               "VALUES ('', '".$site_id."', '".$title."', '".$category."', '".$location."', '".$summary."', '".$description."', '".$status."', '".$datecreated."', '".$lastmodified."')";
      
      db_Connect('hcg_public_master');
      $result = db_Execute($query);
      
      if ($result == false) {
         $jobs['error_msg'] = "There was an error saving this record: ".db_ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
      } else {
         $f->reset();
         $result = 1;
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      // A. set the form variables

      if (!empty($passed_vars)) {  // there's been an error...
         $passed_vars['datecreated'] = date("F j, Y - g:i a");
         $passed_vars['lastmodified'] = date("F j, Y - g:i a");
         $passed_vars['action'] = "create";
         $f->assignArray($passed_vars);
         //set some $jobs variables for the main template
         $jobs['datecreated'] = $passed_vars['datecreated'];
         $jobs['lastmodified'] = $passed_vars['lastmodified'];
         $jobs['status'] = $passed_vars['status'];
      } else {
         $jobs_form['lastaction'] = $_SESSION['user_last_action'] + 1;
         $jobs_form['datecreated'] = date("F j, Y - g:i a");
         $jobs_form['lastmodified'] = date("F j, Y - g:i a");
         $jobs_form['action'] = "create";
         $f->assignArray($jobs_form);
         //set some $jobs variables for the main template
         $jobs['datecreated'] = $jobs_form['datecreated'];
         $jobs['lastmodified'] = $jobs_form['lastmodified'];
         $jobs['status'] = 1;
      }

      // B. set other template variables
      
      // get list of all categories to build select menu
      $query2 = "SELECT categoryid, categoryname FROM jobs_category " .
                "WHERE siteid LIKE \"".$site_id."\" " .
                "AND status = 1";

      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      $cat_list = db_GetAll($query2);
      
      // restructure data for formsess options
      for ($i=0; $i<count($cat_list); $i++) {
         $category_list[$cat_list[$i]['categoryid']] = $cat_list[$i]['categoryname'];
      }

      if (!empty($category_list)) {
         $jobs['category_exists'] = true;
      } else {
         $jobs['category_exists'] = false;
      }
      $jobs['siteid'] = $site_id;

      $t->assign("jobs", $jobs);
      $t->assign("category_list", $category_list);
	
      $t->setTplPath("jobs_adm_createjob.tpl");
      echo $t->fetch("jobs_adm_createjob.tpl");
      $result = "in_progress";
   }
   return $result;
}


// ------------------------------------------------------------------------
// adm_edit_job
//
// ------------------------------------------------------------------------

function adm_edit_job($job_num) 
{
   global $_HCG_GLOBAL;

   $passed_vars = $_HCG_GLOBAL['passed_vars'];
   unset($passed_vars['action']);
   unset($passed_vars['job_num']);

   $display_form = true;
   $save_data = false;

   // create template object for form
   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   // create formsess object to manipulate form variables
   $f = new Formsess('editjob');
   //$f->reset();

   // PART 1: Check if the form was submitted/correctly filled out
   
   if (!empty($passed_vars)) {
   
      extract($passed_vars, EXTR_OVERWRITE);

      // used to detect if the form has already been saved once
      if ($lastaction > $_SESSION['user_last_action']) {
         $already_saved = false;
      } else {
         $already_saved = true;
         echo "form has already been saved";
      }

      $f->reset("errors");
      $f->performCheck();
      if ($f->hasErrors() == false) {
         $display_form = false;
         $save_data = true;
      }      
   }   
   
   // PART 2: Save the data if applicable

   if (($save_data == true) && ($already_saved == false)) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;
      
      // process field contents
      $title = process_jobs_field($title);
      //$category = process_jobs_field($category);
      $location = process_jobs_field($location);
      $summary = process_jobs_field($summary, true);
      $description = process_jobs_field($description, true);
      //$status = process_jobs_field($status);
      $datecreated = process_jobs_field($datecreated);
      $lastmodified = process_jobs_field($lastmodified);
      
      $query = "UPDATE jobs ". 
               "SET title = '".$title."', ".
               "categoryid = ".$category.", ".
               "location = '".$location."', ".
               "summary = '".$summary."', ".
               "description = '".$description."', ".
               "lastmodified = '".$lastmodified."' ".
               "WHERE jobid = ".$job_num;
      
      db_Connect('hcg_public_master');
      $result = db_Execute($query);
      
      if ($result == false) {
         $jobs['error_msg'] = "There was an error updating this record: ".db_ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
      } else {
         $f->reset();
         $result = 1;
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
   
      // A. set the form variables
      
      if (!empty($passed_vars)) {  // there's been an error...
         $passed_vars['lastmodified'] = date("F j, Y - g:i a");
         $passed_vars['action'] = "edit";
         $f->assignArray($passed_vars);
         //set some $jobs variables for the main template
         $jobs['datecreated'] = $passed_vars['datecreated'];
         $jobs['lastmodified'] = $passed_vars['lastmodified'];
         $jobs['status'] = $passed_vars['status'];
         $jobs['siteid'] = $passed_vars['siteid'];
         $jobs['categoryid'] = $passed_vars['category'];
      } else {
         $query1 = "SELECT * FROM jobs " . 
                   "WHERE jobid = ".$job_num;
         $jobs_form = db_GetRow($query1);
         // unprocess field contents
         foreach ($jobs_form as $key => $value) {
            $jobs_form[$key] = unprocess_jobs_field($value);
         }
         $jobs_form['lastmodified'] = date("F j, Y - g:i a");
         $jobs_form['lastaction'] = $_SESSION['user_last_action'] + 1;
         $jobs_form['action'] = "edit";
         $jobs_form['job_num'] = $job_num;
         $f->assignArray($jobs_form);
         //set some $jobs variables for the main template
         $jobs['datecreated'] = $jobs_form['datecreated'];
         $jobs['lastmodified'] = $jobs_form['lastmodified'];
         $jobs['status'] = $jobs_form['status'];
         $jobs['siteid'] = $jobs_form['siteid'];
         $jobs['categoryid'] = $jobs_form['categoryid'];
      }

      // get list of all categories to build select menu   
      $query2 = "SELECT * FROM jobs_category " .
                "WHERE siteid LIKE \"".$jobs['siteid']."\" " .
                "AND status = 1";
      $cat_list = db_GetAll($query2);

      // restructure data for formsess options
      for ($i=0; $i<count($cat_list); $i++) {
         $category_list[$cat_list[$i]['categoryid']] = $cat_list[$i]['categoryname'];
      }

      if (!empty($category_list)) {
         $jobs['category_exists'] = true;
      } else {
         $jobs['category_exists'] = false;
      }
      
      $t->assign("jobs", $jobs);
      $t->assign("category_list", $category_list);
	
      $t->setTplPath("jobs_adm_editjob.tpl");
      echo $t->fetch("jobs_adm_editjob.tpl");
      $result = "in_progress";
   }
   return $result;
}

// ######################### CATEGORY FUNCTIONS ###########################

// ------------------------------------------------------------------------
// adm_change_cat_status
//
// ------------------------------------------------------------------------

function adm_change_cat_status($cat_num, $lastaction) 
{
   // detect if page is being refreshed
   if ($lastaction > $_SESSION['user_last_action']) {
      $_SESSION['user_last_action']++;

      $update_record = true;

      $query1 = "SELECT status, categoryname FROM jobs_category " .
                "WHERE categoryid = $cat_num";
   
      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      $row = db_GetRow($query1);
   
      if ($row['status'] == 1) {
         $query2 = "SELECT * FROM jobs ".
                   "WHERE categoryid = ".$cat_num." ".
                   "AND status <= 1";
         $probs = db_GetAll($query2);
         if (count($probs) == 0) {
            $new_status = 0;
         } else {
            $update_record = false;
            $error_msg = "You cannot deactivate the category \"".$row['categoryname']."\" because there are jobs that use it. Please return to Manage Jobs and change those jobs to another category.";
         }
      } elseif ($row['status'] == 0) {
         $new_status = 1;
      }
   
      if ($update_record == true) {
         $query3 = "UPDATE jobs_category " . 
                   "SET status = ".$new_status." " .
                   "WHERE categoryid = ".$cat_num;
         db_Execute($query3);
         return 1;
      } else {
         return $error_msg;
      }
   } else {
      return 1;
   }
}


// ------------------------------------------------------------------------
// adm_trash_cat
//
// ------------------------------------------------------------------------

function adm_trash_cat($cat_num) 
{
   db_Connect('hcg_public_master');
   
   $query1 = "SELECT * FROM jobs ".
             "WHERE categoryid = ".$cat_num." ".
             "AND status <= 1";

   $probs = db_GetAll($query1);

   if (count($probs) == 0) {

      $query2 = "UPDATE jobs_category " . 
                "SET status = 2 " .
                "WHERE categoryid = ".$cat_num;
      db_Execute($query2);
      return 1;

   } else {
   
      $query1 = "SELECT categoryname FROM jobs_category " .
                "WHERE categoryid = $cat_num";
   
      db_SetFetchMode(ADODB_FETCH_ASSOC);
      $row = db_GetRow($query1);

      $error_msg = "You cannot delete the category \"".$row['categoryname']."\" because there are jobs that use it. Please return to Manage Jobs and change those jobs to another category.";
      return $error_msg;
      
   }
}


// ------------------------------------------------------------------------
// adm_create_cat
//
// ------------------------------------------------------------------------

function adm_create_cat($site_id) 
{
   global $_HCG_GLOBAL;
   
   $passed_vars = $_HCG_GLOBAL['passed_vars'];
   unset($passed_vars['action']);
   
   $display_form = true;
   $save_data = false;
   
   // create template object for form
   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   // create formsess object to manipulate form variables
   $f = new Formsess('createcategory');
   
   // PART 1: Check if the form was submitted/correctly filled out
   
   if (!empty($passed_vars)) {
   
      extract($passed_vars, EXTR_OVERWRITE);

      // detect if the form has already been saved once
      if ($lastaction > $_SESSION['user_last_action']) {
         $already_saved = false;
      } else {
         $already_saved = true;
      }
      
      $f->reset("errors");
      $f->performCheck();
      if ($f->hasErrors() == false) {
         $display_form = false;
         $save_data = true;
      }
   }   
   
   // PART 2: Save the data if applicable

   if (($save_data == true) && ($already_saved == false)) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;
      
      // process field contents
      $categoryname = process_jobs_field($categoryname);
      $status = process_jobs_field($status);
      $datecreated = process_jobs_field($datecreated);
      $lastmodified = process_jobs_field($lastmodified);
      
      $query = "INSERT INTO jobs_category (categoryid, siteid, categoryname, status, datecreated, lastmodified)" .
               "VALUES ('', '".$site_id."', '".$categoryname."', '".$status."', '".$datecreated."', '".$lastmodified."')";
      
      db_Connect('hcg_public_master');
      $result = db_Execute($query);
      
      if ($result == false) {
         $category['error_msg'] = "There was an error saving this record: ".db_ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
      } else {
         $f->reset();
         $result = 1;
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      // A. set the form variables

      if (!empty($passed_vars)) {  // there's been an error...
         $passed_vars['datecreated'] = date("F j, Y - g:i a");
         $passed_vars['lastmodified'] = date("F j, Y - g:i a");
         $passed_vars['action'] = "create";
         $f->assignArray($passed_vars);
         //set some $category variables for the main template
         $category['datecreated'] = $passed_vars['datecreated'];
         $category['lastmodified'] = $passed_vars['lastmodified'];
         $category['status'] = $passed_vars['status'];
      } else {
         $category_form['lastaction'] = $_SESSION['user_last_action'] + 1;
         $category_form['datecreated'] = date("F j, Y - g:i a");
         $category_form['lastmodified'] = date("F j, Y - g:i a");
         $category_form['action'] = "create";
         $f->assignArray($category_form);
         //set some $category variables for the main template
         $category['datecreated'] = $category_form['datecreated'];
         $category['lastmodified'] = $category_form['lastmodified'];
         $category['status'] = 1;
      }

      // B. set other template variables
      
      $category['siteid'] = $site_id;

      $t->assign("category", $category);
	
      $t->setTplPath("jobs_adm_createcategory.tpl");
      echo $t->fetch("jobs_adm_createcategory.tpl");   
      $result = "in_progress";
   }
   return $result;
}


// ------------------------------------------------------------------------
// adm_edit_cat
//
// ------------------------------------------------------------------------

function adm_edit_cat($site_id, $cat_num) 
{
   global $_HCG_GLOBAL;

   $passed_vars = $_HCG_GLOBAL['passed_vars'];
   unset($passed_vars['action']);
   unset($passed_vars['cat_num']);
   
   $display_form = true;
   $save_data = false;

   // create template object for form
   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   // create formsess object to manipulate form variables
   $f = new Formsess('editcategory');
   //$f->reset();

   // PART 1: Check if the form was submitted/correctly filled out
   
   if (!empty($passed_vars)) {
   
      extract($passed_vars, EXTR_OVERWRITE);

      // used to detect if the form has already been saved once
      if ($lastaction > $_SESSION['user_last_action']) {
         $already_saved = false;
      } else {
         $already_saved = true;
      }

      $f->reset("errors");
      $f->performCheck();
      if ($f->hasErrors() == false) {
         $display_form = false;
         $save_data = true;
      }      
   }   
   
   // PART 2: Save the data if applicable

   if (($save_data == true) && ($already_saved == false)) {
   
      $display_form = false;
      $_SESSION['user_last_action']++;
      
      // process field contents
      $categoryname = process_jobs_field($categoryname);
      $status = process_jobs_field($status);
      $datecreated = process_jobs_field($datecreated);
      $lastmodified = process_jobs_field($lastmodified);
      
      $query = "UPDATE jobs_category ". 
               "SET categoryname = '".$categoryname."', ".
               "lastmodified = '".$lastmodified."' ".
               "WHERE categoryid = ".$cat_num;
               
      db_Connect('hcg_public_master');
      $result = db_Execute($query);
      
      if ($result == false) {
         $category['error_msg'] = "There was an error updating this record: ".db_ErrorMsg();
         $_SESSION['user_last_action']--;
         $display_form = true;
      } else {
         $f->reset();
         $result = 1;
      }
   }
   
   // PART 3: Display the form if applicable

   if ($display_form == true) {
   
      db_Connect('hcg_public_master');
      db_SetFetchMode(ADODB_FETCH_ASSOC);
   
      // A. set the form variables
      
      if (!empty($passed_vars)) {  // there's been an error...
         $passed_vars['lastmodified'] = date("F j, Y - g:i a");
         $passed_vars['action'] = "edit";
         $f->assignArray($passed_vars);
         //set some $category variables for the main template
         $category['datecreated'] = $passed_vars['datecreated'];
         $category['lastmodified'] = $passed_vars['lastmodified'];
         $category['status'] = $passed_vars['status'];
         $category['siteid'] = $passed_vars['siteid'];
      } else {
         $query1 = "SELECT * FROM jobs_category " . 
                   "WHERE categoryid = ".$cat_num;
         $category_form = db_GetRow($query1);
         // unprocess field contents
         foreach ($category_form as $key => $value) {
            $category_form[$key] = unprocess_jobs_field($value);
         }
         $category_form['lastmodified'] = date("F j, Y - g:i a");
         $category_form['lastaction'] = $_SESSION['user_last_action'] + 1;
         $category_form['action'] = "edit";
         $category_form['cat_num'] = $cat_num;
         $f->assignArray($category_form);
         //set some $category variables for the main template
         $category['datecreated'] = $category_form['datecreated'];
         $category['lastmodified'] = $category_form['lastmodified'];
         $category['status'] = $category_form['status'];
         $category['siteid'] = $category_form['siteid'];
      }

      $t->assign("category", $category);
	
      $t->setTplPath("jobs_adm_editcategory.tpl");
      echo $t->fetch("jobs_adm_editcategory.tpl");   
      $result = "in_progress";
   }
   return $result;


}


// ------------------------------------------------------------------------
// adm_build_jobs_db
//   I'm not sure I want this function, but I wanted to include it in case
//   it makes sense in the future.
//
// ------------------------------------------------------------------------

function adm_build_jobs_db() 
{

   $query1 = "CREATE TABLE jobs (" .
             "jobid int unsigned NOT NULL auto_increment PRIMARY KEY, " .
             "siteid char(2) NOT NULL, " .
             "title char(255) NOT NULL, " .
             "categoryid int NOT NULL, " .
             "location char(35) NOT NULL, " .
             "summary text, " .
             "description text NOT NULL, " .
             "status int NOT NULL, " .
             "datecreated char(30) NOT NULL, " .
             "lastmodified char(30) NOT NULL)";

   $query2 = "CREATE TABLE jobs_category (" .
             "categoryid int unsigned NOT NULL auto_increment PRIMARY KEY, " .
             "siteid char(2) NOT NULL, " .
             "categoryname char(255) NOT NULL, " .
             "status int NOT NULL, " .
             "datecreated char(30) NOT NULL, " .
             "lastmodified char(30) NOT NULL)";

}


// ------------------------------------------------------------------------
// process_jobs_field
//
// ------------------------------------------------------------------------
function process_jobs_field($field, $keep_html = false)
{
   $field = addslashes($field);
   $field = htmlentities($field, ENT_QUOTES);
   //$field = htmlspecialchars($field);  don't think this is needed.
   
   if ($keep_html == true) {
      $trans = array (
         "&lt;b&gt;" => "<b>",
         "&lt;B&gt;" => "<b>",
         "&lt;/b&gt;" => "</b>",
         "&lt;/B&gt;" => "</b>",

         "&lt;i&gt;" => "<i>",
         "&lt;I&gt;" => "<i>",
         "&lt;/i&gt;" => "</i>",
         "&lt;/I&gt;" => "</i>",

         "&lt;u&gt;" => "<u>",
         "&lt;U&gt;" => "<u>",
         "&lt;/u&gt;" => "</u>",
         "&lt;/U&gt;" => "</u>",
      );
      $field = strtr($field, $trans);
   }
   
   return $field;
}


// ------------------------------------------------------------------------
// unprocess_jobs_field
//
// ------------------------------------------------------------------------
function unprocess_jobs_field($field)
{
   $field = stripslashes($field);
   $field = html_entity_decode($field, ENT_QUOTES);
      
   return $field;
}


// ------------------------------------------------------------------------
// reset_forms
//  Makes sure that all the forms in this application are reset if the
//  user leaves the form using the back button, for example.
//
// ------------------------------------------------------------------------
function reset_forms()
{
   global $_HCG_GLOBAL;

   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   $f = new Formsess('createjob');
   $f->reset();

   $f = new Formsess('editjob');
   $f->reset();

   $f = new Formsess('createcategory');
   $f->reset();

   $f = new Formsess('editcategory');
   $f->reset();
}

?>
