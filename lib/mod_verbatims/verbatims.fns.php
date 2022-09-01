<?php

// =========================================================================
// verbatims.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once("template.class.php");
require_once("dbi_adodb.inc.php");
require_once("mod_core/core.inc.php");


// ------------------------------------------------------------------------
// TAG: random_verbatim
//
// ------------------------------------------------------------------------

function random_verbatim($cat_name, $tpl = "random_verbatim.tpl") 
{
   global $_HCG_GLOBAL;
   
   // get faq data

   $query = 'SELECT * '.
            'FROM vbm_verbatim, vbm_category, vbm_verbatim_category '. 
            'WHERE vbm_category.CategoryName = "'.$cat_name.'" '.
            'AND vbm_verbatim.VerbatimID = vbm_verbatim_category.VerbatimID '.
            'AND vbm_category.CategoryID = vbm_verbatim_category.CategoryID '.
            'AND vbm_verbatim.Status = "active"';

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $verbatims = $db->GetAll($query);
   
   $max = count($verbatims) - 1;
   $verbatim = $verbatims[mt_rand(0, $max)];

   foreach ($verbatim AS $key => $value) {
      $verbatim[$key] = stripslashes($value);
   }

   $t = new HCG_Smarty;

   $t->assign("verbatim", $verbatim);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}

// ------------------------------------------------------------------------
// TAG: verbatim_list
//
// ------------------------------------------------------------------------

function verbatim_list($cat_name, $tpl = "verbatim_list.tpl") 
{
   global $_HCG_GLOBAL;
   
   // get verbatim data

   $query = 'SELECT * '.
            'FROM vbm_verbatim, vbm_category, vbm_verbatim_category '. 
            'WHERE vbm_category.CategoryName = "'.$cat_name.'" '.
            'AND vbm_verbatim.VerbatimID = vbm_verbatim_category.VerbatimID '.
            'AND vbm_category.CategoryID = vbm_verbatim_category.CategoryID '.
            'AND vbm_verbatim.Status = "active"';

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $verbatims = $db->GetAll($query);
   
   foreach ($verbatims AS $item => $verbatim) {
      foreach ($verbatim AS $key => $value) {
         $new_verbatims[$item][$key] = stripslashes($value);
      }
   }

   $t = new HCG_Smarty;

   $t->assign("verbatims", $new_verbatims);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}

// ------------------------------------------------------------------------
// TAG: random_verbatim_list
//
// ------------------------------------------------------------------------

function random_verbatim_list($cat_name, $num_verbatims = 10, $tpl = "random_verbatim.tpl") 
{
   echo 'This would produce a list of '.$num_verbatims.' random verbatims from the "'.$cat_name.'" list if it were implemented.';
}

?>
