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

require_once 'boadmin3.inc.php';


// ---------------------------------------------------------------------
// TAG: boadmin_init
//
// ---------------------------------------------------------------------
function boadmin_init()
{
   global $_HCG_GLOBAL;

   # E-mail address where administration notifications should be sent.
   $_HCG_GLOBAL['boa_admin_mail'] = "japplega@hain-celestial.com";

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

   $_HCG_GLOBAL['boa']['leftnav'][] = array ('section' => 'home',
                                             'label' => 'Home',
                                             'level' => 2,
                                             'URL' => 'index.php');
   $_HCG_GLOBAL['boa']['leftnav'][] = array ('section' => 'aoi_admin',
                                             'label' => 'AOI Administration',
                                             'level' => 3,
                                             'URL' => 'aoi_admin.php');
   $_HCG_GLOBAL['boa']['leftnav'][] = array ('section' => 'salesperson',
                                             'label' => 'Salesperson Lookup',
                                             'level' => 2,
                                             'URL' => 'salesperson.php');
   $_HCG_GLOBAL['boa']['leftnav'][] = array ('section' => 'user_new',
                                             'label' => 'New User Request',
                                             'level' => 2,
                                             'URL' => 'user_new.php');
   $_HCG_GLOBAL['boa']['leftnav'][] = array ('section' => 'user_edit',
                                             'label' => 'Change User Request',
                                             'level' => 2,
                                             'URL' => 'user_edit.php');
   
   $_HCG_GLOBAL['boa']['tabs'] = array 
   (
      'Regions' => 'aoi_regions.php',
      'Brands' => 'aoi_brands.php',
      'Source System' => 'aoi_sourcesystems.php',
   );

}


// ---------------------------------------------------------------------
// TAG: boadmin_reqaccess
//   returns the required access for the current page. This is defined
//   in the boadmin_init() function above.
//
// ---------------------------------------------------------------------
function boadmin_reqaccess($section)
{
   global $_HCG_GLOBAL;
   
   $reqaccess = 10;
   foreach($_HCG_GLOBAL['boa']['leftnav'] as $key => $value) {
      if(strtolower($section) == $value['section']) {
         $reqaccess = $value['level'];
      }
   }
   return $reqaccess;
}


// ---------------------------------------------------------------------
// TAG: boadmin_nav
//   Build navigation based on rights.
//
// ---------------------------------------------------------------------
function boadmin_nav($access_level, $section)
{
   global $_HCG_GLOBAL;

   $data = array();
   $nav = $_HCG_GLOBAL['boa']['leftnav'];
   
   $count = 0;
   for ($i=0; $i<count($nav); $i++) {
      if ($nav[$i]['level'] <= $access_level) {
         $data[$count] = $nav[$i];
         if ($nav[$i]['section'] == $section) {
            $data[$count]['highlight'] = 1;
         } else {
            $data[$count]['highlight'] = 0;
         }
         $count++;
      }
   }
   
   $t = new HCG_Smarty;

   $t->assign("data", $data);
	
   $t->setTplPath("boa3_nav.tpl");
   echo $t->fetch("boa3_nav.tpl");
   
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
   
   $channels = boa_get_user_channel_list($user_id);
   $divisions = boa_get_user_division_list($user_id);
   $regions = boa_get_user_region_list($user_id);

   $t = new HCG_Smarty;

   $t->assign("user_id", $user_id);
   $t->assign("channels", $results[0]);
   $t->assign("divisions", $results[1]);
   $t->assign("regions", $results[2]);
   
   $t->setTplPath("boa3_salesperson.tpl");
   echo $t->fetch("boa3_salesperson.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_user_edit
//
// ---------------------------------------------------------------------
function boadmin_user_edit()
{
   global $_HCG_GLOBAL;
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // get passed-in variables
   $user_id = isset($_HCG_GLOBAL['passed_vars']['user_id']) ? $_HCG_GLOBAL['passed_vars']['user_id'] : "";
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";

   // Get channels, divisions, and regions
   $channels = boa_get_channels($db);
   $divisions = boa_get_divisions($db);
   $regions = boa_get_regions($db, "dwspersondesc");

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
      $mail['region_list'] = $db->GetAll($query4);
      
      boa_send_email($mail, "boa3_user_edit_mail.tpl");
   }

   # Load other data.

   if ($user_id) {
      $my_regions = boa_get_my_regions($db, $user_id);
      
      # mark which regions are already selected
      for ($i=0; $i<count($regions); $i++) {
         if (in_array($regions[$i]['DWSPERSONCODE'], $my_regions)) {
            $regions[$i]['selected'] = 1;
         } else {
            $regions[$i]['selected'] = 0;
         }
      }
   }

   $user_list = boa_get_user_list($db);

   $t = new HCG_Smarty;

   $t->assign("what", $what);
   $t->assign("user_id", $user_id);
   $t->assign("user_list", $user_list);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   
   $t->setTplPath("boa3_user_edit.tpl");
   echo $t->fetch("boa3_user_edit.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_user_new
//
// ---------------------------------------------------------------------
function boadmin_user_new()
{
   global $_HCG_GLOBAL;
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // get passed-in variables
   $user_id = isset($_HCG_GLOBAL['passed_vars']['user_id']) ? $_HCG_GLOBAL['passed_vars']['user_id'] : "";
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";

   // Get channels, divisions, and regions
   $channels = boa_get_channels($db);
   $divisions = boa_get_divisions($db);
   $regions = boa_get_regions($db, "dwspersondesc");

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
      $mail['region_list'] = $db->GetAll($query4);
      
      boa_send_email($mail, "boa3_user_new_mail.tpl");
   }

   $t = new HCG_Smarty;

   $t->assign("what", $what);
   $t->assign("locations", $_HCG_GLOBAL['boa_user_locations']);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   
   $t->setTplPath("boa3_user_new.tpl");
   echo $t->fetch("boa3_user_new.tpl");
}


// ---------------------------------------------------------------------
// TAG: boadmin_aoi_admin
//
// ---------------------------------------------------------------------
function boadmin_aoi_admin()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   if (isset($_HCG_GLOBAL['passed_vars']['user_id']))
   {
      $user_id = $_HCG_GLOBAL['passed_vars']['user_id'];
   }
   elseif (isset($_SESSION['boa']['user_id']) && $_SESSION['boa']['user_id'] != "")
   {
      $user_id = $_SESSION['boa']['user_id'];
   }
   else
   {
      $user_id = "";
   }
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // if we're saving in this function, we are copying an existing record
   // as opposed to saving something new
   if ($what == "save" && $_HCG_GLOBAL['passed_vars']['copy_to']) {

      $copyto_id = $_HCG_GLOBAL['passed_vars']['copy_to'];

      // Remove all data for copyto user and start fresh.
      $query2 = "DELETE FROM areaofinterest ".
                "WHERE user_id = '".trim($copyto_id)."'";
      $rs = $db->Execute($query2);
      if (!$rs) {
         echo "aoi_admin:1:".$db->ErrorMsg()."<br>";
      }

      // find all records for source user and resave with copyto's ID
      $query3 = "SELECT * FROM areaofinterest ".
                "WHERE user_id = '".trim($user_id)."'";
      $rs = $db->GetAll($query3);
      if (!$rs) {
         echo "aoi_admin:2:".$db->ErrorMsg()."<br>";
      }
      for ($i=0; $i<count($rs); $i++) {
         $query4 = "INSERT INTO areaofinterest ".
                   "(user_id, ";
         if ($rs[$i]['AOI'] != null) {
            $query4 .= "aoi, ";
         }
         $query4 .= "brandcode, aoi_rec_level, trans_date) ".
                    "VALUES ('".trim($copyto_id)."',";
         if ($rs[$i]['AOI'] != null) {
            $query4 .= " ".$rs[$i]['AOI'].",";
         }
         $query4 .= " '".$rs[$i]['BRANDCODE']."', '".
                    $rs[$i]['AOI_REC_LEVEL']."', ".
                    "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
         $result = $db->Execute($query4);
         if (!$result) {
            echo "aoi_admin:3:".$db->ErrorMsg()."<br>".$query4."<br>";
         }
      }
      $user_id = $copyto_id;
      $what = "load";
   }

   // Remove this user.
   if ($what == "remove") {
      $query6 = "DELETE FROM areaofinterest ".
                "WHERE user_id = '".trim($user_id)."'";
      $rs = $db->Execute($query6);
      if (!$rs) {
         echo "aoi_admin:4:".$db->ErrorMsg()."<br>";
      }
      $user_id = "";
      $_SESSION['boa']['user_id'] = "";
   }
   
   // E-mail this data to someone.
   if ($what == "email" || (isset($_HCG_GLOBAL['passed_vars']['email_user']) && $_HCG_GLOBAL['passed_vars']['email_user'] == true)) {
      boadmin_aoi_send_email($db, $user_id);
      $what = "load";
   }

   // A user was selected or entered, so go to regions page
   if ($what == "load") {
      $_SESSION['boa']['user_id'] = $user_id;
      header('location: '.$HCG_GLOBAL['base_url'].'/boa3/aoi_regions.php');
   }

   $user_list = boa_get_user_list($db);

   $t = new HCG_Smarty;
   
   $t->debugging = TRUE;

   $t->assign("what", $what);
   $t->assign("user_id", $user_id);
   $t->assign("user_list", $user_list);
   
   $t->setTplPath("boa3_aoi_admin.tpl");
   echo $t->fetch("boa3_aoi_admin.tpl");

}


// ---------------------------------------------------------------------
// TAG: boadmin_aoi_regions
//
// ---------------------------------------------------------------------
function boadmin_aoi_regions()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   if ($_SESSION['boa']['user_id'] == "") {
      header('location: '.$HCG_GLOBAL['base_url'].'/boa3/aoi_admin.php');
   } else {
      $user_id = $_SESSION['boa']['user_id'];
   }
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // Get regions
   $regions = boa_get_regions($db, "dwspersondesc");

   // Save this user.

   if ($what == "save") {

      if ($user_id) {
         // Remove all data for this user and start fresh.
         $query2 = "DELETE FROM areaofinterest ".
                   "WHERE user_id = '".trim($user_id)."' ".
                   "AND aoi_rec_level = 'SalesPerson'";
         $rs = $db->Execute($query2);
         if (!$rs) {
            echo "aoi_regions:1:".$db->ErrorMsg()."<br>";
         }
      }

      // If this person is a super-user, give access to ALL regions.

      if (in_array("SuperUser", $_HCG_GLOBAL['passed_vars']['usertype'])) {
         $query3 = "INSERT INTO areaofinterest ".
                   "(user_id, aoi, aoi_rec_level, trans_date) ".
                   "VALUES ('".trim($user_id)."', -1000, 'SalesPerson', ".
                   "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
         $rs = $db->Execute($query3);
         if (!$rs) {
            echo "aoi_regions:1:".$db->ErrorMsg()."<br>";
         }
         for ($i=0; $i<count($regions); $i++) {
            if (!empty($regions[$i]['DWSPERSONCODE'])) {
               $query4 = "INSERT INTO areaofinterest ".
                         "(user_id, aoi, aoi_rec_level, trans_date) ".
                         "VALUES ('".trim($user_id)."', ".
                          $regions[$i]['DWSPERSONCODE'].", 'SalesPerson', ".
                         "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
               $rs = $db->Execute($query4);
               if (!$rs) {
                  echo "aoi_regions:3:".$db->ErrorMsg()."<br>".$query4."<br>";
               }
            }
         }
         
      // If this person is not a superuser, give access to selected regions.
      
      } else {
  
         foreach ($_HCG_GLOBAL['passed_vars']['region'] as $aoi) {
            $query5 = "INSERT INTO areaofinterest ".
                      "(user_id, aoi, aoi_rec_level, trans_date) ".
                      "VALUES ('".trim($user_id)."', ".$aoi.", ".
                      "'SalesPerson', TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
            $rs = $db->Execute($query5);
            if (!$rs) {
               echo "aoi_regions:4:".$db->ErrorMsg()."<br>";
            }
         }
      }
   }

   // E-mail this data to someone.
   if ($_HCG_GLOBAL['passed_vars']['email_user'] == true) {
      boadmin_aoi_send_email($db, $user_id);
   }

   // Check if user is a superuser and set to selected if they are
   if ($user_id) {
      $query8 = "SELECT * FROM areaofinterest ".
                "WHERE user_id = '".$user_id."' ".
                "AND aoi_rec_level = 'SalesPerson' ".
                "AND aoi = -1000";
      $superuser = $db->GetAll($query8);
      if (count($superuser) > 0) {
         $isSuperUser = 1;
      }
   }

   $channels = boa_get_user_channel_list($user_id);
   $divisions = boa_get_user_division_list($user_id);
   $regions = boa_get_user_region_list($user_id, "dwspersondesc");

   $col_length = (count($regions) / 3) + 1;

   $t = new HCG_Smarty;

   $t->assign("tabs", $_HCG_GLOBAL['boa']['tabs']);
   $t->assign("this_tab", "Regions");
   $t->assign("what", $what);
   $t->assign("user_id", $user_id);
   $t->assign("isSuperUser", $isSuperUser);
   $t->assign("channels", $channels);
   $t->assign("divisions", $divisions);
   $t->assign("regions", $regions);
   $t->assign("col_length", $col_length);
   $t->assign("user_email", getEmailAddress($user_id));
   
   $t->setTplPath("boa3_aoi_regions.tpl");
   echo $t->fetch("boa3_aoi_regions.tpl");

}


// ---------------------------------------------------------------------
// TAG: boadmin_aoi_brands
//
// ---------------------------------------------------------------------
function boadmin_aoi_brands()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   if ($_SESSION['boa']['user_id'] == "") {
      header('location: '.$HCG_GLOBAL['base_url'].'/boa3/aoi_admin.php');
   } else {
      $user_id = $_SESSION['boa']['user_id'];
   }
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // Get brands
   $brands = boa_get_brands($db);

   // Save this user.

   if ($what == "save") {

      if ($user_id) {
         // Remove all data for this user and start fresh.
         $query2 = "DELETE FROM areaofinterest ".
                   "WHERE user_id = '".trim($user_id)."' ".
                   "AND aoi_rec_level = 'Brand'";
         $rs = $db->Execute($query2);
         if (!$rs) {
            echo "aoi_brands:1:".$db->ErrorMsg()."<br>";
         }
      }

      // If this person is a super-user, give access to ALL brands.

      if (in_array("SuperUser", $_HCG_GLOBAL['passed_vars']['usertype'])) {
         $query3 = "INSERT INTO areaofinterest ".
                   "(user_id, aoi, aoi_rec_level, trans_date) ".
                   "VALUES ('".trim($user_id)."', -1000, 'Brand', ".
                   "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
         $rs = $db->Execute($query3);
         if (!$rs) {
            echo "aoi_brands:2:".$db->ErrorMsg()."<br>";
         }
         for ($i=0; $i<count($brands); $i++) {
            if (!empty($brands[$i]['BRANDCODE'])) {
               $query4 = "INSERT INTO areaofinterest ".
                         "(user_id, brandcode, aoi_rec_level, trans_date) ".
                         "VALUES ('".trim($user_id)."', ".
                         "'".$brands[$i]['BRANDCODE']."', 'Brand', ".
                         "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
               $rs = $db->Execute($query4);
               if (!$rs) {
                  echo "aoi_brands:3:".$db->ErrorMsg()."<br>";
               }
            }
         }
         
      // If this person is not a superuser, give access to selected brands.
      
      } else {
  
         foreach ($_HCG_GLOBAL['passed_vars']['brand'] as $aoi) {
            $query5 = "INSERT INTO areaofinterest ".
                      "(user_id, brandcode, aoi_rec_level, trans_date) ".
                      "VALUES ('".trim($user_id)."', '".$aoi."', ".
                      "'Brand', TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
            $rs = $db->Execute($query5);
            if (!$rs) {
               echo "aoi_brands:4:".$db->ErrorMsg()."<br>";
            }
         }
      }
   }

   // E-mail this data to someone.
   if ($_HCG_GLOBAL['passed_vars']['email_user'] == true) {
      boadmin_aoi_send_email($db, $user_id);
   }

   // Check if user is a superuser and set to selected if they are
   if ($user_id) {
      $query8 = "SELECT * FROM areaofinterest ".
                "WHERE user_id = '".$user_id."' ".
                "AND aoi_rec_level = 'Brand' ".
                "AND aoi = -1000";
      $superuser = $db->GetAll($query8);
      if (count($superuser) > 0) {
         $isSuperUser = 1;
      }
   }

   $brands = boa_get_user_brand_list($user_id);

   $col_length = (count($brands) / 3) + 1;

   $t = new HCG_Smarty;

   $t->assign("tabs", $_HCG_GLOBAL['boa']['tabs']);
   $t->assign("this_tab", "Brands");
   $t->assign("what", $what);
   $t->assign("brands", $brands);
   $t->assign("user_id", $user_id);
   $t->assign("isSuperUser", $isSuperUser);
   $t->assign("col_length", $col_length);
   $t->assign("user_email", getEmailAddress($user_id));
   
   $t->setTplPath("boa3_aoi_brands.tpl");
   echo $t->fetch("boa3_aoi_brands.tpl");

}


// ---------------------------------------------------------------------
// TAG: boadmin_aoi_sourcesystems
//
// ---------------------------------------------------------------------
function boadmin_aoi_sourcesystems()
{
   global $_HCG_GLOBAL;
   
   // get passed-in variables
   if ($_SESSION['boa']['user_id'] == "") {
      header('location: '.$HCG_GLOBAL['base_url'].'/boa3/aoi_admin.php');
   } else {
      $user_id = $_SESSION['boa']['user_id'];
   }
   $what = isset($_HCG_GLOBAL['passed_vars']['what']) ? $_HCG_GLOBAL['passed_vars']['what'] : "";
   
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // Get source systems
   $sourcesystems = boa_get_sourcesystems($db);

   // Save this user.

   if ($what == "save") {

      if ($user_id) {
         // Remove all data for this user and start fresh.
         $query2 = "DELETE FROM areaofinterest ".
                   "WHERE user_id = '".trim($user_id)."' ".
                   "AND aoi_rec_level = 'SourceSystem'";
         $rs = $db->Execute($query2);
         if (!$rs) {
            echo "aoi_sourcesystems:1:".$db->ErrorMsg()."<br>";
         }
      }

      // If this person is a super-user, give access to ALL source systems.

      if (in_array("SuperUser", $_HCG_GLOBAL['passed_vars']['usertype'])) {
         $query3 = "INSERT INTO areaofinterest ".
                   "(user_id, aoi, aoi_rec_level, trans_date) ".
                   "VALUES ('".trim($user_id)."', -1000, 'SourceSystem', ".
                   "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
         $rs = $db->Execute($query3);
         if (!$rs) {
            echo "aoi_sourcesystems:2:".$db->ErrorMsg()."<br>";
         }
         for ($i=0; $i<count($sourcesystems); $i++) {
            if ($sourcesystems[$i]['SOURCESYSTEMKEY'] != NULL) {
               $query4 = "INSERT INTO areaofinterest ".
                         "(user_id, aoi, aoi_rec_level, trans_date) ".
                         "VALUES ('".trim($user_id)."', ".
                          $sourcesystems[$i]['SOURCESYSTEMKEY'].", ".
                         "'SourceSystem', ".
                         "TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
               $rs = "aoi_sourcesystems:3:".$db->Execute($query4);
               if (!$rs) {
                  echo $db->ErrorMsg()."<br>";
               }
            }
         }
         
      // If this person is not a superuser, give access to selected 
      // source systems.
      
      } else {
  
         foreach ($_HCG_GLOBAL['passed_vars']['sourcesystem'] as $aoi) {
            $query5 = "INSERT INTO areaofinterest ".
                      "(user_id, aoi, aoi_rec_level, trans_date) ".
                      "VALUES ('".trim($user_id)."', ".$aoi.", ".
                      "'SourceSystem', TO_DATE('" .date("Ymd")."','YYYYMMDD'))";
            $rs = $db->Execute($query5);
            if (!$rs) {
               echo "aoi_sourcesystems:4:".$db->ErrorMsg()."<br>";
            }
         }
      }
   }

   // E-mail this data to someone.
   if ($_HCG_GLOBAL['passed_vars']['email_user'] == true) {
      boadmin_aoi_send_email($db, $user_id);
   }

   // Check if user is a superuser and set to selected if they are
   if ($user_id) {
      $query8 = "SELECT * FROM areaofinterest ".
                "WHERE user_id = '".$user_id."' ".
                "AND aoi_rec_level = 'SourceSystem' ".
                "AND aoi = -1000";
      $superuser = $db->GetAll($query8);
      if (count($superuser) > 0) {
         $isSuperUser = 1;
      }
   }

   $sourcesystems = boa_get_user_sourcesystem_list($user_id);

   $col_length = (count($sourcesystems) / 3) + 1;

   $t = new HCG_Smarty;

   $t->assign("tabs", $_HCG_GLOBAL['boa']['tabs']);
   $t->assign("this_tab", "Source System");
   $t->assign("what", $what);
   $t->assign("sourcesystems", $sourcesystems);
   $t->assign("user_id", $user_id);
   $t->assign("isSuperUser", $isSuperUser);
   $t->assign("col_length", $col_length);
   $t->assign("user_email", getEmailAddress($user_id));
      
   $t->setTplPath("boa3_aoi_sourcesystems.tpl");
   echo $t->fetch("boa3_aoi_sourcesystems.tpl");

}

/**
 * E-mail this data to someone.
 */
function boadmin_aoi_send_email(&$db, $user_id)
{
   global $_HCG_GLOBAL;
   
   $my_types = array();
   if ($user_id) {
      $query8 = "SELECT DISTINCT aoi_rec_level FROM areaofinterest ".
                "WHERE user_id = '".$user_id."'";
      $my_types_list = $db->GetAll($query8);
      foreach ($my_types_list as $row) {
         array_push($my_types, trim($row['AOI_REC_LEVEL']));
      }
   }

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
   
   // Check if user is a superuser and set to selected if they are
   if ($user_id) {
      $query8 = "SELECT aoi_rec_level FROM areaofinterest ".
                "WHERE user_id = '".$user_id."' ".
                "AND aoi = -1000";
      $superuser = $db->GetAll($query8);
      foreach ($superuser as $key => $value) {
         $mail['superuser'][trim($value['AOI_REC_LEVEL'])] = 1;
      }
   }
   
   $mail['user_id'] = $user_id;
   $mail['user_type'] = implode(", ", $my_types);
         
   $mail['channels'] = boa_get_user_channel_list($user_id);
   $mail['divisions'] = boa_get_user_division_list($user_id);
   $mail['regions'] = boa_get_user_region_list($user_id);
   $mail['brands'] = boa_get_user_brand_list($user_id);
   $mail['sourcesystems'] = boa_get_user_sourcesystem_list($user_id);
   
   boa_send_email($mail, "boa3_aoi_admin_mail.tpl");

}

function print_pre($data)
{
   echo "<pre>";
   print_r($data);
   echo "</pre>";
}

?>
