<?php

// =========================================================================
// admin.adm.php
// written by Jim Applegate
//
// =========================================================================

define("DEBUG", 0);

require_once 'template.class.php';
require_once 'includes/general.inc.php';

function adm_admin()
{
   $module = "admin";
   $default_action = "adm_admin_home";
   
   require 'controllers/call_action.inc.php';
}

// -------------------------------------------------------------------
// site_list
// -------------------------------------------------------------------
function adm_admin_site_list()
{
   global $_HCG_GLOBAL;
   global $_TABLE;

   $_TABLE['table_id'] = 'site';
   require 'screens/site.list.screen.php';
   
   $sql_select = '*';
   $sql_from   = '';
   $sql_where  = '';
   
   $module = "admin";

   require 'controllers/list1.inc.php';
}

// -------------------------------------------------------------------
// site_add
// -------------------------------------------------------------------
function adm_admin_site_add()
{
   // add root record to menu database
   // $menu['MenuText'] = "<BrandName>";
   // $menu['LinkText'] = "<BrandName>";
   // $menu['Description'] = "<BrandName> Website Administration";
   // $menu['URL'] = /admin.php?task=admin_links_display&site=<SiteId>;
   
}

// -------------------------------------------------------------------
// site_delete
// -------------------------------------------------------------------
function adm_admin_site_delete()
{
}

// -------------------------------------------------------------------
// help_display
// -------------------------------------------------------------------
function adm_admin_help_display()
{
   global $_HCG_GLOBAL;
   global $_TABLE;

   $_TABLE['table_id'] = 'help';

   $module = "admin";

   require 'controllers/help.inc.php';
}

// -------------------------------------------------------------------
// home_display
// -------------------------------------------------------------------
function adm_admin_home()
{
   $tpl="adm/home.tpl";
   
   // create a class instance of the main database table
   require_once "mod_menu/tables/menu.class.php";
   $menu_obj = new Menu;
   $menu_obj->sql_select = 'menu.*';
   $menu_obj->sql_from    = "site, menu";
   $menu_obj->sql_where   = 'menu.Parent=1 AND menu.MenuText=site.BrandName';
//   if (isset($sql_orderby)) {
//      $site_obj->setDefaultOrderby($sql_orderby);
//   }
   $menu_list = $menu_obj->getData();
   
//   echo "<pre>";
//   print_r($menu_list);
//   echo "</pre>";
   
   $t = new HCG_Smarty;

   $t->assign("sites", $menu_list);
   $t->assign("site_count", count($menu_list));
	
   $t->setTplPath($tpl);
   $t->display($tpl);


}

// -------------------------------------------------------------------
// links_display
// -------------------------------------------------------------------
function adm_admin_links_display()
{

}

?>