<?php 

// =========================================================================
//  boadmin2.inc.php
//
//  Written by Jim Applegate
//  based on code by Jeff Schroeder (jeff@neobox.net)
//
// =========================================================================

require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';
require_once 'mod_ldapauth/ldapauth.inc.php';


/**
 * boa_get_user_channel_list
 */
function boa_get_user_channel_list($user_id, $orderby = "dwchannelcode")
{
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $channels = boa_get_channels($db, $orderby);
   $my_channels = boa_get_my_channels($db, $user_id);
   
   // determine what items should be selected
   
   for ($i=0; $i<count($channels); $i++) {
      if (in_array($channels[$i]['DWCHANNELCODE'], $my_channels)) {
         $channels[$i]['selected'] = 1;
      } else {
         $channels[$i]['selected'] = 0;
      }
   }
   return $channels;
}


/**
 * boa_get_user_division_list
 */
function boa_get_user_division_list($user_id, $orderby = "dwdivisioncode")
{
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $divisions = boa_get_divisions($db, $orderby);
   $my_divisions = boa_get_my_divisions($db, $user_id);
   
   // determine what items should be selected
   
   for ($i=0; $i<count($divisions); $i++) {
      if (in_array($divisions[$i]['DWDIVISIONCODE'], $my_divisions)) {
         $divisions[$i]['selected'] = 1;
      } else {
         $divisions[$i]['selected'] = 0;
      }
   }
   return $divisions;
}


/**
 * boa_get_user_region_list
 */
function boa_get_user_region_list($user_id, $orderby = "dwspersoncode")
{
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $regions = boa_get_regions($db, $orderby);
   $my_regions = boa_get_my_regions($db, $user_id);
   
   // determine what items should be selected
   
   for ($i=0; $i<count($regions); $i++) {
      if (in_array($regions[$i]['DWSPERSONCODE'], $my_regions)) {
         $regions[$i]['selected'] = 1;
      } else {
         $regions[$i]['selected'] = 0;
      }
   }
   return $regions;
}


/**
 * boa_get_user_brand_list
 */
function boa_get_user_brand_list($user_id)
{
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $brands = boa_get_brands($db);
   $my_brands = boa_get_my_brands($db, $user_id);
   
   // determine what items should be selected
   
   for ($i=0; $i<count($brands); $i++) {
      if (in_array($brands[$i]['BRANDCODE'], $my_brands)) {
         $brands[$i]['selected'] = 1;
      } else {
         $brands[$i]['selected'] = 0;
      }
   }
   return $brands;
}


/**
 * boa_get_user_sourcesystem_list
 */
function boa_get_user_sourcesystem_list($user_id)
{
   $db = HCGNewConnection('boa');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $sourcesys = boa_get_sourcesystems($db);
   $my_sourcesys = boa_get_my_sourcesystems($db, $user_id);
   
   // determine what items should be selected   
   for ($i=0; $i<count($sourcesys); $i++) {
      if (in_array($sourcesys[$i]['SOURCESYSTEMKEY'], $my_sourcesys)) {
         $sourcesys[$i]['selected'] = 1;
      } else {
         $sourcesys[$i]['selected'] = 0;
      }
   }   
   return $sourcesys;
}


// ---------------------------------------------------------------------
// boa_get_channels
//
// ---------------------------------------------------------------------
function boa_get_channels(&$db, $orderby = "dwchannelcode")
{
   $query = "SELECT DISTINCT dwchannelcode, dwchanneldesc ".
            "FROM regionxlate ".
            "WHERE dwchannelcode > 0 ".
            "ORDER BY ".$orderby;
   $channels = $db->GetAll($query);
   return $channels;
}


// ---------------------------------------------------------------------
// boa_get_divisions
//
// ---------------------------------------------------------------------
function boa_get_divisions(&$db, $orderby = "dwdivisioncode")
{
   $query = "SELECT DISTINCT dwdivisioncode, dwdivisiondesc ".
            "FROM regionxlate ".
            "WHERE dwdivisioncode > 0 ".
            "ORDER BY ".$orderby;
   $divisions = $db->GetAll($query);
   return $divisions;
}


// ---------------------------------------------------------------------
// boa_get_regions
//
// ---------------------------------------------------------------------
function boa_get_regions(&$db, $orderby = "dwspersoncode")
{
   $query = "SELECT DISTINCT dwspersoncode, dwspersondesc ".
            "FROM regionxlate ".
            "WHERE dwspersoncode > 0 ".
            "OR dwspersoncode = -1 ".
            "ORDER BY ".$orderby;
   $regions = $db->GetAll($query);
   return $regions;
}


// ---------------------------------------------------------------------
// boa_get_brands
//
// ---------------------------------------------------------------------
function boa_get_brands(&$db)
{
   $query = "SELECT DISTINCT brandcode, branddesc ".
            "FROM product ".
            "WHERE productreclevel = 'Brand' ".
            "AND brandcode IS NOT NULL ".
            "ORDER BY branddesc";
   $brands = $db->GetAll($query);
   return $brands;
}


// ---------------------------------------------------------------------
// boa_get_sourcesystems
//
// ---------------------------------------------------------------------
function boa_get_sourcesystems(&$db)
{
   $query = "SELECT DISTINCT sourcesystemkey, description ".
            "FROM sourcesystem ".
            "ORDER BY description";
   $sourcesystems = $db->GetAll($query);
   return $sourcesystems;
}


// ---------------------------------------------------------------------
// boa_get_user_list
//
// ---------------------------------------------------------------------
function boa_get_user_list(&$db)
{
   $query = "SELECT DISTINCT user_id FROM areaofinterest ".
            "ORDER BY user_id";
   $user_list = $db->GetAll($query);
   if (!$user_list) {
      echo $db->ErrorMsg();
   }

   return $user_list;
}


// ---------------------------------------------------------------------
// boa_get_my_channels
//
// ---------------------------------------------------------------------
function boa_get_my_channels(&$db, $user_id)
{
   $my_channels = array();

   $query = "SELECT DISTINCT aoi FROM areaofinterest ".
            "WHERE user_id = '".$user_id."' ".
            "AND aoi_rec_level = 'SalesPerson' ".
            "AND aoi > 0 ".
            "ORDER BY aoi";
   $my_aois = $db->GetAll($query);
   
   foreach ($my_aois as $row) {
      $channel = substr($row['AOI'], 0, 1) . "0000";
      if (!in_array($channel, $my_channels)) {
         array_push($my_channels, $channel);
      }
   }
   return $my_channels;
}


// ---------------------------------------------------------------------
// boa_get_my_divisions
//
// ---------------------------------------------------------------------
function boa_get_my_divisions(&$db, $user_id)
{
   $my_divisions = array();

   $query = "SELECT DISTINCT aoi FROM areaofinterest ".
            "WHERE user_id = '".$user_id."' ".
            "AND aoi_rec_level = 'SalesPerson' ".
            "AND aoi > 0 ".
            "ORDER BY aoi";
   $my_aois = $db->GetAll($query);
   
   foreach ($my_aois as $row) {
      $division = substr($row['AOI'], 0, 3) . "00";
      if (!in_array($division, $my_divisions)) {
         array_push($my_divisions, $division);
      }
   }
   return $my_divisions;
}


// ---------------------------------------------------------------------
// boa_get_my_regions
//
// ---------------------------------------------------------------------
function boa_get_my_regions(&$db, $user_id)
{
   $my_regions = array();

   $query = "SELECT DISTINCT aoi FROM areaofinterest ".
            "WHERE user_id = '".$user_id."' ".
            "AND aoi_rec_level = 'SalesPerson' ".
            "AND aoi > 0 ".
            "ORDER BY aoi";
   $my_aois = $db->GetAll($query);
   
   foreach ($my_aois as $row) {
      array_push($my_regions, $row['AOI']);
   }
   return $my_regions;
}


// ---------------------------------------------------------------------
// boa_get_my_brands
//
// ---------------------------------------------------------------------
function boa_get_my_brands(&$db, $user_id)
{
   $my_brands = array();

   $query = "SELECT DISTINCT brandcode FROM areaofinterest ".
            "WHERE user_id = '".$user_id."' ".
            "AND aoi_rec_level = 'Brand' ".
            "AND brandcode IS NOT NULL ".
            "ORDER BY brandcode";
   $my_brandcodes = $db->GetAll($query);
   
   foreach ($my_brandcodes as $row) {
      array_push($my_brands, $row['BRANDCODE']);
   }
   return $my_brands;
}


// ---------------------------------------------------------------------
// boa_get_my_sourcesystesm
//
// ---------------------------------------------------------------------
function boa_get_my_sourcesystems(&$db, $user_id)
{
   $my_sourcesystems = array();

   $query = "SELECT DISTINCT aoi FROM areaofinterest ".
            "WHERE user_id = '".$user_id."' ".
            "AND aoi_rec_level = 'SourceSystem' ".
            "AND aoi IS NOT NULL ".
            "ORDER BY aoi";
   $my_aois = $db->GetAll($query);
   
   foreach ($my_aois as $row) {
      array_push($my_sourcesystems, $row['AOI']);
   }
   return $my_sourcesystems;
}


// ---------------------------------------------------------------------
// boa_send_email
//
// ---------------------------------------------------------------------
function boa_send_email($mail, $tpl)
{
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   $m = new HCG_Smarty;
   $m->assign("mail", $mail);
   $m->setTplPath($tpl);
   $mail_content = $m->fetch($tpl);
   
//   echo "<pre>".$tpl.": ".$mail_content."</pre>";
   
   $fd = popen($sendmail,"w");
   fputs($fd, stripslashes($mail_content)."\n");
   pclose($fd);
}


?>
