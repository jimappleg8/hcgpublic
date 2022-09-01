<?php 

// =========================================================================
//  ldapauth.fns.php
//  written by Jim Applegate
//  last modified: 19 Aug 2003
// =========================================================================


require_once('mod_core/core.inc.php');
require_once('template.class.php');
require_once('ldapauth.inc.php');


// ------------------------------------------------------------------------
// TAG: access_level()
//   returns an access level number:
//     1 = all (not logged in)
//     2 = company (logged in, not a group member)
//     3 = group (logged in, group member)
//   If a group name is not included, it simply returns whether the person
//   is logged in or not (1 or 2).
//
// ------------------------------------------------------------------------
function access_level($group_name = "", $server = 'live')
{
   if (is_logged_in()) 
   {
      if ($group_name == "") 
      {
         $access_level = 2;
      }
      elseif (in_ad_group($_SESSION['valid_user'], $group_name))
      {
         $access_level = 3;
      }
      else
      {
         $access_level = 2;
      }
   }
   else
   {
      $access_level = 1;
      $_SESSION['common_name'] = '';
      $_SESSION['user_email'] = '';
   }
   return $access_level;
}


// ------------------------------------------------------------------------
// TAG: ldap_login
//   Logs into the system.
//
// ------------------------------------------------------------------------
function ldap_login($server = 'live')
{
   global $_HCG_GLOBAL;
   
   $userid = isset($_HCG_GLOBAL['passed_vars']['userid']) ? $_HCG_GLOBAL['passed_vars']['userid'] : '';
   $password = isset($_HCG_GLOBAL['passed_vars']['password']) ? $_HCG_GLOBAL['passed_vars']['password'] : '';
   
   if ($userid && $password)
   {
      if ( ! is_logged_in())
      {
         $login_result = login($userid, $password, false, $server);
      }
      else
      {
         $login_result = 2;  // the user is already logged in
      }
      
      $t = new HCG_Smarty;

      $t->assign("login_result", $login_result);
      $t->assign("return_url", $_SESSION['return_url']);

      $t->setTplPath("ldapauth/ldap_login_results.tpl");
      return $t->fetch("ldapauth/ldap_login_results.tpl");
   }
   else
   {
      $_SESSION['return_url'] = $_SESSION['last_page'];
      $form_action = $_HCG_GLOBAL['php_self'];

      $t = new HCG_Smarty;

      $t->assign("userid", $userid);
      $t->assign("form_action", $form_action);
      $t->assign("return_url", $_SESSION['return_url']);

      $t->setTplPath("ldapauth/ldap_login_form.tpl");
      return $t->fetch("ldapauth/ldap_login_form.tpl");
   }

}


// ------------------------------------------------------------------------
// TAG: ldap_logout
//   Logs out of system. Based on code in "PHP and MySQL Web Development"
//   by Luke Welling and Laura Thomson, pages 444-445.
//
// ------------------------------------------------------------------------
function ldap_logout()
{
   $logout_result = logout();
   
   $t = new HCG_Smarty;

   $t->assign("logout_result", $logout_result);
   $t->assign("return_url", $_SESSION['last_page']);

   $t->setTplPath("ldapauth/ldap_logout_results.tpl");
   return $t->fetch("ldapauth/ldap_logout_results.tpl");
}


// ------------------------------------------------------------------------
// TAG: is_logged_in
//
// ------------------------------------------------------------------------
function is_logged_in()
{
   if (isset($_SESSION['valid_user']))
   {
      $user_status = 1;
   }
   else
   {
      $user_status = 0;
   }
   return $user_status;
}


// ------------------------------------------------------------------------
// TAG: is_group_member
//
// ------------------------------------------------------------------------
function is_group_member($userid, $g_name, $g_attr="uniqueMember", $o_class="groupOfUniqueNames", $server) 
{
   return in_ad_group($userid, $g_name);
}


// ------------------------------------------------------------------------
// TAG: test_cracklib
//
// ------------------------------------------------------------------------
function test_cracklib()
{
   global $_HCG_GLOBAL;
   
   // extract the passed variables from the global variable instead of
   // having them passed as parameters.
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      extract($_HCG_GLOBAL['passed_vars'], EXTR_OVERWRITE);
   }
   
   echo "<div align=\"center\">\n";
   echo "<hr noshade size=\"1\" width=\"60%\">\n";

   if (!empty($test_pass)) {
      $check = crack_check($test_pass);
      if (!$check) {
      // the password isn't "strong" enough
         $diag = crack_getlastmessage();
         echo "\"" . $test_pass . "\" " . $diag . ".\n";
      } else {
         echo "\"" . $test_pass . "\" is a strong password.\n";
      }
   }
   
   echo "<br>\n";
   echo "<form action=\"/utils/cracklib.php\">\n";
   echo "<input type=\"text\" name=\"test_pass\">\n";
   echo "<br><input type=\"submit\" value=\"Test Password\">\n";
   echo "<hr noshade size=\"1\" width=\"60%\">\n";
   echo "</div>";

}



?>