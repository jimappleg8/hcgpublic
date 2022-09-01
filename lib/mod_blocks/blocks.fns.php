<?php

// =========================================================================
// recipes.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';


/**
 * Return block text based on block name and language
 *
 */
function block($block_name, $site_id = "default")
{
   global $_HCG_GLOBAL;
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT Block FROM block " . 
            "WHERE BlockName LIKE '".$block_name."' ".
            "AND Language = '".strtolower($_SESSION['language'])."' ".
            "AND SiteID = '".$site_id."'";

   $block = $db->GetOne($query);
   
   return $block;
   
}

/**
 * Return page information based on page name and language
 *
 */
function page($page_name, $site_id = "default")
{
   global $_HCG_GLOBAL;
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT Title, MetaDescription, MetaKeywords FROM page " . 
            "WHERE PageName LIKE '".$page_name."' ".
            "AND Language = '".strtolower($_SESSION['language'])."' ".
            "AND SiteID = '".$site_id."'";

   $page = $db->GetAll($query);
   
   return $page[0];
   
}




?>
