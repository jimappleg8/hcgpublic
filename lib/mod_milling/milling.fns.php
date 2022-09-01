<?php

// =========================================================================
// milling.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';


// ------------------------------------------------------------------------
// TAG: milling_panel
//
// ------------------------------------------------------------------------

function milling_panel($tpl = "milling_panel.tpl") 
{
   global $_HCG_GLOBAL;
   
   include 'http://colts.ctea.com/operations/Health%20and%20Safety/lemongrass.inc';
   
   if (empty($schedule))
   {
      exit;
   }
   
//   echo "<pre>"; print_r($schedule); echo "</pre>";

   $day[0]['timestamp'] = time();
   $day[1]['timestamp'] = $day[0]['timestamp'] + 86400;
   $day[2]['timestamp'] = $day[1]['timestamp'] + 86400;
   $day[3]['timestamp'] = $day[2]['timestamp'] + 86400;
   $day[4]['timestamp'] = $day[3]['timestamp'] + 86400;
   $day[5]['timestamp'] = $day[4]['timestamp'] + 86400;
   $day[6]['timestamp'] = $day[5]['timestamp'] + 86400;

   for ($i=0; $i<count($day); $i++)
   {
      $day[$i]['date'] = date('m-d-Y', $day[$i]['timestamp']);
      $day[$i]['sm_date'] = date('n/j', $day[$i]['timestamp']);
      $day[$i]['lemongrass'] = 0;
      $day[$i]['color'] = 'blue';
      for ($j=0; $j<count($schedule); $j++)
      {
         if ($day[$i]['date'] == $schedule[$j]['date'])
         {
            $day[$i]['lemongrass'] = 1;
            $day[$i]['color'] = $schedule[$j]['color'];
         }
      }
      $day[$i]['weekday'] = date('D', $day[$i]['timestamp']);
   }
   
//   echo "<pre>"; print_r($day); echo "</pre>";
   
   $t = new HCG_Smarty;
   
   $t->assign("day", $day);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);   

}

// This tag is not currently being used. We've decided to offer a link 
// to the download the milling schedule instead.

function milling_list($tpl = "milling_panel.tpl") 
{
   global $_HCG_GLOBAL;
   
   include 'http://colts.ctea.com/operations/Health%20and%20Safety/lemongrass.inc';
   
   if (empty($schedule))
   {
      exit;
   }
   
   for ($i=0; $i<count($schedule); $i++)
   {

   }
   
   $t = new HCG_Smarty;
   
   $t->assign("day", $day);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);

}

?>
