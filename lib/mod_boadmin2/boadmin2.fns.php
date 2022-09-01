<?php 

// =========================================================================
//  boadmin2.fns.php
//
//  Written by Jim Applegate
//  based on code by Jeff Schroeder (jeff@neobox.net)
//
//  Requested Changes:
//   - New Feature: Who has access to this region?
//
// =========================================================================

require_once 'boadmin2.inc.php';


// ---------------------------------------------------------------------
// TAG: boadmin_init
//
// ---------------------------------------------------------------------
function boadmin_init($db_name)
{
   global $_HCG_GLOBAL;

   # E-mail address where administration notifications should be sent.
   $_HCG_GLOBAL['boa_admin_mail'] = "jim.applegate@hain.com";

   # User locations.
   $_HCG_GLOBAL['boa_user_locations'] = array
   (
      "Boulder",
      "Irwindale",
      "Melville",
      "Remote",
      "Yves"
   );

   # Navigation.  The array keys are the display name shown in the left-hand
   # navigation bar.  The array values are the access level required to use
   # that page, followed by the URL of the page.

   $_HCG_GLOBAL['boa_navigation'] = array
   (
      "Home" => "2 index.php",
      "AOI Administration" => "3 aoi_admin.php",
//      "Region Translations" => "3 region_xlate.php",
      "Salesperson Lookup" => "2 salesperson.php",
      "New User Request" => "2 user_new.php",
      "Change User Request" => "2 user_edit.php",
   );

   # User types.

   $_HCG_GLOBAL['boa_aoi_user_types'][0] = array
   (
      "code" => "SalesPerson",
      "desc" => "Regional User"
   );
   $_HCG_GLOBAL['boa_aoi_user_types'][1] = array
   (
      "code" => "SuperUser",
      "desc" => "Super User"
   );
   
   # Database connection.
   
   $_HCG_GLOBAL['boadmin2_db'] = HCGNewConnection($db_name);
   $_HCG_GLOBAL['boadmin2_db']->SetFetchMode(ADODB_FETCH_ASSOC);


}


// ---------------------------------------------------------------------
// TAG: boadmin_reqaccess
//   returns the required access for the current page. This is defined
//   in the boadmin_init() function above.
//
// ---------------------------------------------------------------------
function boadmin_reqaccess()
{
   global $_HCG_GLOBAL;
   
   $reqaccess = 10;
   foreach($_HCG_GLOBAL['boa_navigation'] as $key => $value) {
      if(strtolower(basename($_SERVER{"PHP_SELF"})) == substr($value, 2)) {
         $reqaccess = substr($value, 0);
      }
   }
   return $reqaccess;
}


// ---------------------------------------------------------------------
// TAG: boadmin_nav
//   Build navigation based on rights.
//
// ---------------------------------------------------------------------
function boadmin_nav($access_level)
{
   global $_HCG_GLOBAL;

   $data = array();
   $data['nav'] = array();

   foreach($_HCG_GLOBAL['boa_navigation'] as $key => $value)
   {
      if (substr($value, 0, 1) <= $access_level)
      {
         $data['nav'][$key] = substr($value, 2);
      }
   }
   
   $t = new HCG_Smarty;

   $t->assign("data", $data);
	
   $t->setTplPath("boa_nav.tpl");
   echo $t->fetch("boa_nav.tpl");
   
}


// ---------------------------------------------------------------------
// TAG: boadmin_tabs
//
// ---------------------------------------------------------------------
function boadmin_tabs($tabs = "", $activetab = "")
{
   global $_HCG_GLOBAL;

   $data = array();
   $data['tabs'] = $tabs; // orginally supplied to the function
   $data['activetab'] = $activetab;  // orginally supplied to the function

   $t = new HCG_Smarty;

   $t->assign("data", $data);
	
   $t->setTplPath("boa_tabs.tpl");
   echo $t->fetch("boa_tabs.tpl");

}


// ---------------------------------------------------------------------
// TAG: boadmin_salesperson
//
// ---------------------------------------------------------------------
function boadmin_salesperson()
{
   $user_id = $_SESSION['valid_user']; # LDAP login name.
   
   $results = boa_get_user_region_list($_HCG_GLOBAL['boadmin2_db'], $user_id);

   $t = new HCG_Smarty;

   $t->assign("user_id", $user_id);
   $t->assign("channels", $results[0]);
   $t->assign("divisions", $results[1]);
   $t->assign("regions", $results[2]);
   
   $t->setTplPath("boa_salesperson.tpl");
   echo $t->fetch("boa_salesperson.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_user_edit
//
// ---------------------------------------------------------------------
function boadmin_user_edit()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   $user_id = isset($_HCG_GLOBAL['passed_vars']['user_id']) ? $_HCG_GLOBAL['passed_vars']['user_id'] : "";
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";

   // Get channels, divisions, and regions
   $channels = boa_get_channels($_HCG_GLOBAL['boadmin2_db']);
   $divisions = boa_get_divisions($_HCG_GLOBAL['boadmin2_db']);
   $regions = boa_get_regions($_HCG_GLOBAL['boadmin2_db'], "dwspersondesc");

   // Save this user.
   
   if ($what == "save") {
   
      // E-mail this information to the BO admin.
      
      $mail['boa_admin'] = $_HCG_GLOBAL['boa_admin_mail'];
      $mail['common_name'] = $_SESSION['common_name'];
      $mail['user_id'] = $user_id;
      $mail['location'] = $_HCG_GLOBAL['passed_vars']['location'];
      $mail['create_reports'] = isset($_HCG_GLOBAL['passed_vars']['option_create_reports']) ? "yes" : "no";
      $mail['view_reports'] = isset($_HCG_GLOBAL['passed_vars']['option_view_reports']) ? "yes" : "no";
      
      $region = $_HCG_GLOBAL['passed_vars']['region'];
      $query4 = "SELECT DISTINCT dwspersoncode, dwspersondesc ".
                "FROM regionxlate ";
      for ($i=0; $i<count($region); $i++) {
         if ($i == 0) {
            $query4 .= "WHERE dwspersoncode = ".$region[$i]." ";
         } else {
            $query4 .= "OR dwspersoncode = ".$region[$i]." ";
         }
      }
      $query4 .= "ORDER BY dwspersoncode";
      $mail['region_list'] = $_HCG_GLOBAL['boadmin2_db']->GetAll($query4);
      
      boa_send_email($mail, "boa_user_edit_mail.tpl");
   }

   # Load other data.

   if ($user_id) {
      $my_regions = boa_get_my_regions($_HCG_GLOBAL['boadmin2_db'], $user_id);
      
      # mark which regions are already selected
      for ($i=0; $i<count($regions); $i++) {
         if (in_array($regions[$i]['dwspersoncode'], $my_regions)) {
            $regions[$i]['selected'] = 1;
         } else {
            $regions[$i]['selected'] = 0;
         }
      }
   }

   $user_list = boa_get_user_list($_HCG_GLOBAL['boadmin2_db']);

   $t = new HCG_Smarty;

   $t->assign("what", $what);
   $t->assign("user_id", $user_id);
   $t->assign("user_list", $user_list);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   
   $t->setTplPath("boa_user_edit.tpl");
   echo $t->fetch("boa_user_edit.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_user_new
//
// ---------------------------------------------------------------------
function boadmin_user_new()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   $user_id = isset($_HCG_GLOBAL['passed_vars']['user_id']) ? $_HCG_GLOBAL['passed_vars']['user_id'] : "";
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";

   // Get channels, divisions, and regions
   $channels = boa_get_channels($_HCG_GLOBAL['boadmin2_db']);
   $divisions = boa_get_divisions($_HCG_GLOBAL['boadmin2_db']);
   $regions = boa_get_regions($_HCG_GLOBAL['boadmin2_db'], "dwspersondesc");

   // Save this user.

   if ($what == "save") {
   
      // E-mail this information to the BO admin.
      
      $mail['boa_admin'] = $_HCG_GLOBAL['boa_admin_mail'];
      $mail['common_name'] = $_SESSION['common_name'];
      $mail['user_name'] = $_HCG_GLOBAL['passed_vars']['user_name'];
      $mail['location'] = $_HCG_GLOBAL['passed_vars']['location'];
      $mail['create_reports'] = isset($_HCG_GLOBAL['passed_vars']['option_create_reports']) ? "yes" : "no";
      $mail['view_reports'] = isset($_HCG_GLOBAL['passed_vars']['option_view_reports']) ? "yes" : "no";
      
      $region = $_HCG_GLOBAL['passed_vars']['region'];
      $query4 = "SELECT DISTINCT dwspersoncode, dwspersondesc ".
                "FROM regionxlate ";
      for ($i=0; $i<count($region); $i++) {
         if ($i == 0) {
            $query4 .= "WHERE dwspersoncode = ".$region[$i]." ";
         } else {
            $query4 .= "OR dwspersoncode = ".$region[$i]." ";
         }
      }
      $query4 .= "ORDER BY dwspersoncode";
      $mail['region_list'] = $_HCG_GLOBAL['boadmin2_db']->GetAll($query4);
      
      boa_send_email($mail, "boa_user_new_mail.tpl");
   }

   $t = new HCG_Smarty;

   $t->assign("what", $what);
   $t->assign("locations", $_HCG_GLOBAL['boa_user_locations']);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   
   $t->setTplPath("boa_user_new.tpl");
   echo $t->fetch("boa_user_new.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_aoi_admin
//
// ---------------------------------------------------------------------
function boadmin_aoi_admin()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   $user_id = isset($_HCG_GLOBAL['passed_vars']['user_id']) ? $_HCG_GLOBAL['passed_vars']['user_id'] : "";
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";
   
   // Get regions
   $regions = boa_get_regions($_HCG_GLOBAL['boadmin2_db'], "dwspersondesc");
   
   // Save this user.

   if ($what == "save") {

      if ($_HCG_GLOBAL['passed_vars']['copy_to']) {
         $user_id = $_HCG_GLOBAL['passed_vars']['copy_to'];
      }

      if ($user_id) {
         // Remove all data for this user and start fresh.
         $query2 = "DELETE FROM areaofinterest ".
                   "WHERE user_id = '".trim($user_id)."'";
         $rs = $_HCG_GLOBAL['boadmin2_db']->Execute($query2);
         if (!$rs) {
            echo $_HCG_GLOBAL['boadmin2_db']->ErrorMsg()."<br>";
         }
      }

      // If this person is a super-user, give access to ALL regions.

      if (in_array("SuperUser", $_HCG_GLOBAL['passed_vars']['usertype'])) {
         $query3 = "INSERT INTO areaofinterest ".
                   "(user_id, aoi, aoi_rec_level, trans_date) ".
                   "VALUES ('".trim($user_id)."', -1000, 'SuperUser', ".
                   "CONVERT(DATETIME, '" .date("Y/m/d")."', 111))";
         $rs = $_HCG_GLOBAL['boadmin2_db']->Execute($query3);
         if (!$rs) {
            echo $_HCG_GLOBAL['boadmin2_db']->ErrorMsg()."<br>";
         }
         for ($i=0; $i<count($regions); $i++) {
            if (!empty($regions[$i]['dwspersoncode'])) {
               $query4 = "INSERT INTO areaofinterest ".
                         "(user_id, aoi, aoi_rec_level, trans_date) ".
                         "VALUES ('".trim($user_id)."', ".
                          $regions[$i]['dwspersoncode'].", 'SalesPerson', ".
                         "CONVERT(DATETIME, '" .date("Y/m/d")."', 111))";
               $rs = $_HCG_GLOBAL['boadmin2_db']->Execute($query4);
               if (!$rs) {
                  echo $_HCG_GLOBAL['boadmin2_db']->ErrorMsg()."<br>";
               }
            }
         }
         
      // If this person is a salesperson, give access to selected regions.
      
      } elseif (in_array("SalesPerson", $_HCG_GLOBAL['passed_vars']['usertype'])) {
  
         foreach ($_HCG_GLOBAL['passed_vars']['region'] as $aoi) {
            $query5 = "INSERT INTO areaofinterest ".
                      "(user_id, aoi, aoi_rec_level, trans_date) ".
                      "VALUES ('".trim($user_id)."', ".$aoi.", ".
                      "'SalesPerson', CONVERT(DATETIME, '" .date("Y/m/d")."', 111))";
            $rs = $_HCG_GLOBAL['boadmin2_db']->Execute($query5);
            if (!$rs) {
               echo $_HCG_GLOBAL['boadmin2_db']->ErrorMsg()."<br>";
            }
         }
      }
   }

   // Remove this user.

   if ($what == "remove") {
   
      $query6 = "DELETE FROM areaofinterest ".
                "WHERE user_id = '".trim($user_id)."'";
      $success = $_HCG_GLOBAL['boadmin2_db']->Execute($query6);
      if ( ! $success)
      {
         echo $_HCG_GLOBAL['boadmin2_db']->ErrorMsg();
         echo "<br />query: ".$query6;
      }
      $user_id = "";
   }

   // Load other data.

   if ($user_id) {
      $query7 = "SELECT DISTINCT aoi FROM areaofinterest ".
                "WHERE user_id = '".$user_id."' ".
                "AND aoi > 0 ".
                "ORDER BY aoi";
      $my_aois = $_HCG_GLOBAL['boadmin2_db']->GetAll($query7);
   }

   $my_types = array();
   if ($user_id) {
      $query8 = "SELECT DISTINCT aoi_rec_level FROM areaofinterest ".
                "WHERE user_id = '".$user_id."'";
      $my_types_list = $_HCG_GLOBAL['boadmin2_db']->GetAll($query8);
      foreach ($my_types_list as $row) {
         array_push($my_types, trim($row['aoi_rec_level']));
      }
   }

   $my_channels = array();
   if ($user_id) {
      foreach ($my_aois as $row) {
         $channel = substr($row['aoi'], 0, 1) . "0000";
         if (!in_array($channel, $my_channels)) {
            array_push($my_channels, $channel);
         }
      }
   }

   $my_divisions = array();
   if ($user_id) {
      foreach ($my_aois as $row) {
         $division = substr($row['aoi'], 0, 3) . "00";
         if (!in_array($division, $my_divisions)) {
            array_push($my_divisions, $division);
         }
      }
   }

   $my_regions = array();
   if ($user_id) {
      foreach ($my_aois as $row) {
         array_push($my_regions, $row['aoi']);
      }
   }

   // E-mail this data to someone.

   if ($what == "email" || $_HCG_GLOBAL['passed_vars']['email_user'] == true) {
      
      $mail['email_user'] = $_HCG_GLOBAL['passed_vars']['email_user'];
      if ($mail['email_user'] == true) {
         $mail['mail_to'] = $_HCG_GLOBAL['passed_vars']['user_email'];
      } else {
         $mail['mail_to'] = $_HCG_GLOBAL['passed_vars']['mail_to'];
         // if administrator entered the user's email...
         if (strtolower($_HCG_GLOBAL['passed_vars']['user_email']) == strtolower($_HCG_GLOBAL['passed_vars']['mail_to'])) {
            $mail['email_user'] = true;
         }
      }
      
      $mail['user_id'] = $user_id;
      $mail['user_type'] = implode(", ", $my_types);
            
      $results = boa_get_user_region_list($_HCG_GLOBAL['boadmin2_db'], $user_id);
      $mail['channels'] = $results[0];
      $mail['divisions'] = $results[1];
      $mail['regions'] = $results[2];
      
      boa_send_email($mail, "boa_aoi_admin_mail.tpl");
   }

   $user_list = boa_get_user_list($_HCG_GLOBAL['boadmin2_db']);

   // Build a list of user types.
   
   $user_types = $_HCG_GLOBAL['boa_aoi_user_types'];
   for ($i=0; $i<count($user_types); $i++)
   {
      $user_types[$i]['selected'] = 0;
      for ($j=0; $j<count($my_types); $j++)
      {
         if ($user_types[$i]['code'] == trim($my_types[$j]))
         {
            $user_types[$i]['selected'] = 1;
         }
      }
   }

   // Get channels and divisions
   $channels = boa_get_channels($_HCG_GLOBAL['boadmin2_db']);
   $divisions = boa_get_divisions($_HCG_GLOBAL['boadmin2_db']);

   // Build a list of channels.

   for ($i=0; $i<count($channels); $i++) {
      if (in_array($channels[$i]['dwchannelcode'], $my_channels)) {
         $channels[$i]['selected'] = 1;
      } else {
         $channels[$i]['selected'] = 0;
      }
   }

   // Build a list of divisions.

   for ($i=0; $i<count($divisions); $i++) {
      if (in_array($divisions[$i]['dwdivisioncode'], $my_divisions)) {
         $divisions[$i]['selected'] = 1;
      } else {
         $divisions[$i]['selected'] = 0;
      }
   }

   // Build a list of regions.

   for ($i=0; $i<count($regions); $i++) {
      if (in_array($regions[$i]['dwspersoncode'], $my_regions)) {
         $regions[$i]['selected'] = 1;
      } else {
         $regions[$i]['selected'] = 0;
      }
   }
   $col_length = (count($regions) / 3) + 1;

   $t = new HCG_Smarty;

   $t->assign("what", $what);
   $t->assign("user_id", $user_id);
   $t->assign("user_list", $user_list);
   $t->assign("user_types", $user_types);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   $t->assign("col_length", $col_length);
   $t->assign("user_email", getEmailAddress($user_id));
   
   $t->setTplPath("boa_aoi_admin.tpl");
   echo $t->fetch("boa_aoi_admin.tpl");

}


?>
