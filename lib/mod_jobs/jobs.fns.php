<?php

// =========================================================================
// jobs.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';


// ------------------------------------------------------------------------
// TAG: job_list
//
// ------------------------------------------------------------------------

function job_list() 
{
   global $_HCG_GLOBAL;
   
   // get job data
   
   $query = "SELECT * FROM jobs " . 
            "WHERE siteid LIKE \"".$_HCG_GLOBAL['site_id']."\" " .
            "AND status = 1";
            
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $jobs = $db->GetAll($query);
   
   $num_jobs = count($jobs);
   
   for ($i=0; $i<count($jobs); $i++) {

      $jobs[$i]['title'] = stripslashes($jobs[$i]['title']);
      $jobs[$i]['category'] = stripslashes($jobs[$i]['category']);
      $jobs[$i]['location'] = stripslashes($jobs[$i]['location']);
      $jobs[$i]['summary'] = stripslashes($jobs[$i]['summary']);
      $jobs[$i]['description'] = stripslashes($jobs[$i]['description']);
      $jobs[$i]['status'] = stripslashes($jobs[$i]['status']);
      $jobs[$i]['datecreated'] = stripslashes($jobs[$i]['datecreated']);
      $jobs[$i]['lastmodified'] = stripslashes($jobs[$i]['lastmodified']);

      // str_replace \n with \n<br>
      $jobs[$i]['description'] = str_replace("\n", "\n<br>", $jobs[$i]['description']);
      $jobs[$i]['summary'] = str_replace("\n", "\n<br>", $jobs[$i]['summary']);

      // find e-mails and replace with mailto: link
      $jobs[$i]['description'] = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', '<a href="mailto:$0">$0</a>', $jobs[$i]['description']);
      $jobs[$i]['summary'] = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', '<a href="mailto:$0">$0</a>', $jobs[$i]['summary']);

      // find any other links and make live
      $jobs[$i]['description'] = preg_replace('/((ht|f)tp:\/\/[^\s&]+)/', '<a href="$1">$1</a>', $jobs[$i]['description']);
      $jobs[$i]['summary'] = preg_replace('/((ht|f)tp:\/\/[^\s&]+)/', '<a href="$1">$1</a>', $jobs[$i]['summary']);

   }
   
   // get category data
   
   $query = "SELECT * FROM jobs_category " . 
            "WHERE siteid LIKE ".$_HCG_GLOBALS['site_id']." " .
            "AND status = 1";
            
   $category = $db->GetAll($query);
   
   //echo "Jobs: ";
   //print_r($jobs);
      
   $t = new HCG_Smarty;
   
   $t->assign("jobs", $jobs);
   $t->assign("category", $category);
   $t->assign("num_jobs", $num_jobs);
	
   $t->setTplPath("jobs_list.tpl");
   echo $t->fetch("jobs_list.tpl");   

}


// ------------------------------------------------------------------------
// TAG: job_detail
//   template needs to handle case where status is 0 or 2. In those cases
//   the job is no longer available.
//
// ------------------------------------------------------------------------

function job_detail($job_num) 
{

   $query = "SELECT * FROM jobs " . 
            "WHERE jobid = ".$job_num;
   
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $jobs = $db->GetRow($query);
   
   $jobs['title'] = stripslashes($jobs['title']);
   $jobs['category'] = stripslashes($jobs['category']);
   $jobs['location'] = stripslashes($jobs['location']);
   $jobs['summary'] = stripslashes($jobs['summary']);
   $jobs['description'] = stripslashes($jobs['description']);
   $jobs['status'] = stripslashes($jobs['status']);
   $jobs['datecreated'] = stripslashes($jobs['datecreated']);
   $jobs['lastmodified'] = stripslashes($jobs['lastmodified']);
   
   // str_replace \n with \n<br>
   $jobs['description'] = str_replace("\n", "\n<br>", $jobs['description']);
   $jobs['summary'] = str_replace("\n", "\n<br>", $jobs['summary']);
   
   // find e-mails and replace with mailto: link
   $jobs['description'] = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', '<a href="mailto:$0">$0</a>', $jobs['description']);
   $jobs['summary'] = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', '<a href="mailto:$0">$0</a>', $jobs['summary']);
   
   // find any other links and make live
   $jobs['description'] = preg_replace('/((ht|f)tp:\/\/[^\s&]+)/', '<a href="$1">$1</a>', $jobs['description']);
   $jobs['summary'] = preg_replace('/((ht|f)tp:\/\/[^\s&]+)/', '<a href="$1">$1</a>', $jobs['summary']);

   $t = new HCG_Smarty;

   $t->assign("jobs", $jobs);
	
   $t->setTplPath("jobs_detail.tpl");
   echo $t->fetch("jobs_detail.tpl");   

}


?>
