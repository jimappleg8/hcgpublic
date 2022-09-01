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


// ---------------------------------------------------------------------
// boa_get_user_region_list
//
// ---------------------------------------------------------------------
function boa_get_user_region_list(&$db, $user_id)
{
   $my_regions = boa_get_my_regions($db, $user_id);
   $channels = boa_get_channels($db);
   $divisions = boa_get_divisions($db);
   $regions = boa_get_regions($db);
   
   // determine what items should be highlighted
   
   for ($i=0; $i<count($channels); $i++)
   {
      for ($j=0; $j<count($divisions); $j++)
      {
         if (substr($divisions[$j]['dwdivisioncode'], 0, 2) != substr($channels[$i]['dwchannelcode'], 0, 2)) continue;

         $at_least_one = false;
      
         for ($k=0; $k<count($regions); $k++)
         {
            if (substr($regions[$k]['dwspersoncode'], 0, 3) != substr($divisions[$j]['dwdivisioncode'], 0, 3)) continue;

            if (in_array($regions[$k]['dwspersoncode'], $my_regions))
            { 
               $regions[$k]['highlight'] = 1;
               $at_least_one = true; 
            }
            else
            { 
               $regions[$k]['highlight'] = 0;
            }

         }
         if ($at_least_one)
         {
            $divisions[$j]['highlight'] = 1;
         }
         else
         {
            $divisions[$j]['highlight'] = 0;
         }
      }
   }
   $results[0] = $channels;
   $results[1] = $divisions;
   $results[2] = $regions;
   return $results;
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
// boa_get_user_list
//
// ---------------------------------------------------------------------
function boa_get_user_list(&$db)
{
   $query = "SELECT DISTINCT user_id FROM areaofinterest ".
            "ORDER BY user_id";
   $user_list = $db->GetAll($query);
   if (!$user_list)
   {
      echo $db->ErrorMsg();
      echo "<pre>"; print_r($_ENV); echo "</pre>";
   }

   return $user_list;
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
            "AND aoi > 0 ".
            "ORDER BY aoi";
   $my_aois = $db->GetAll($query);
   
   foreach ($my_aois as $row)
   {
      array_push($my_regions, $row['aoi']);
   }
   return $my_regions;
}


// ---------------------------------------------------------------------
// boa_send_email
//
// ---------------------------------------------------------------------
function boa_send_email($mail, $tpl)
{
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail))
   {
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
