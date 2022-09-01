<?php 

// =========================================================================
//  ldapauth.inc.php
//  written by Jim Applegate
//  last modified: 20 Aug 2003
// =========================================================================

require_once 'classes/adLDAP/adLDAP.php';


// -------------------------------------------------------------------------
// function login
//   Logs into the system.
//
// -------------------------------------------------------------------------

function login ($userid, $password, $test = false, $server = 'live') {

   set_error_handler("authError");

   $options = array(
      'domain_controllers' => array('bowdc02.hvntdom.hain-celestial.com'),
      'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_username'        => 'CN=Data Warehouse',
      'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_password'        => 'd8awar3z',
   );
   $adldap = new adLDAP($options);
   
   // get the user's information
   $userinfo = $adldap->user_info($userid, array('cn', 'dn'));
   $cn = $userinfo[0]['cn'][0];
   $dn = $userinfo[0]['dn'];
   $suffix = str_replace('CN='.$cn, '', $dn);
   
   $adldap->_account_suffix = $suffix;
   $authUser = $adldap->authenticate('CN='.$cn, $password, TRUE);
//   echo 'userid = '.$userid.'<br />';
//   echo 'cn = '.$cn.'<br />';
//   echo 'dn = '.$dn.'<br />';
//   echo 'suffix = '.$suffix.'<br />';
//   echo 'password = '.$password.'<br />';
//   echo 'authUser = '.$authUser; exit;

   if ($authUser == TRUE)
   {
      if ($test == TRUE)
      {
         $user_status = 1;
      }
      else
      {
         // get the user's common name
         $name = getCommonName($userid, $server);
         $_SESSION['common_name'] = ( ! $name) ? $userid : $name;
      
         // get the user's email address
         $email = getEmailAddress($userid, $server);
         $_SESSION['user_email'] = ( ! $email) ? "" : $email;
      
         $_SESSION['valid_user'] = $userid;
         $_SESSION['valid_passwd'] = $password;
         $user_status = 1;
      }
   }
   else
   {
      $user_status = 0;
   }
   
   return $user_status;
}


// -------------------------------------------------------------------------
// function getCommonName
//   returns the common name for the username supplied
//
// -------------------------------------------------------------------------

function getCommonName($userid, $server = 'live')
{
   $options = array(
      'domain_controllers' => array('bowdc02.hvntdom.hain-celestial.com'),
      'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_username'        => 'CN=Data Warehouse',
      'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_password'        => 'd8awar3z',
   );
   $adldap = new adLDAP($options);

   $userinfo = $adldap->user_info($userid, array('mail', 'displayname'));
   
   return $userinfo[0]['displayname'][0];
}


// -------------------------------------------------------------------------
// function getEmailAddress
//   returns the Email Address for the username supplied
//
// -------------------------------------------------------------------------

function getEmailAddress($userid, $server = 'live')
{
   $options = array(
      'domain_controllers' => array('bowdc02.hvntdom.hain-celestial.com'),
      'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_username'        => 'CN=Data Warehouse',
      'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_password'        => 'd8awar3z',
   );
   $adldap = new adLDAP($options);

   $userinfo = $adldap->user_info($userid, array('mail'));
   
   return $userinfo[0]['mail'][0];
}


// ------------------------------------------------------------------------
// TAG: in_ad_group
//
// ------------------------------------------------------------------------
function in_ad_group($userid, $group) 
{
   $options = array(
      'domain_controllers' => array('bowdc02.hvntdom.hain-celestial.com'),
      'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_username'        => 'CN=Data Warehouse',
      'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
      'ad_password'        => 'd8awar3z',
   );
   $adldap = new adLDAP($options);
   
   return ($adldap->user_ingroup($userid, $group) == TRUE) ? 1 : 0;
}

// -------------------------------------------------------------------------
// function logout
//   Logs out of the system.
//
// -------------------------------------------------------------------------

function logout ()
{
   global $_HCG_GLOBAL;

   // store to test if they *were* logged in
   $old_user = (isset($_SESSION['valid_user'])) ? $_SESSION['valid_user'] : '';
   unset($_SESSION['valid_user']);
   unset($_SESSION['valid_passwd']);
   unset($_SESSION['common_name']);
   setcookie (session_name(), '', (time () - 2592000), '/', '', 0);

   if ( ! empty($old_user))
   {
      if (isset($_SESSION['valid_user']))
      {
         // they were logged in but could not be logged out
         $logout_result = 0;
      }
      else
      {
         // if they were logged in and are now logged out
         $logout_result = 1;
      }
   }
   else
   {
      // if they were not logged in but came to this page somehow.
      $logout_result = 2;
   }
   return $logout_result;
}


// -------------------------------------------------------------------------
// function authError
//   Empty function to avoid getting warnings displayed.
//
// -------------------------------------------------------------------------

function authError ($error_type, $error_msg) {

}


?>