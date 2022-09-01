<?php

// =========================================================================
// config.inc.php
// written by Jim Applegate
// =========================================================================

define('DEBUG', 0);

// set whether a notice is displayed on Contact Us forms
// indicating that mail response may be slow.
$_HCG_GLOBAL['display_mail_disclaimer'] = false;
$_HCG_GLOBAL['mail_disclaimer'] = "Due to a heavy increase in our email volume at this time, responses to email may take up to 30 days. For further assistance our Consumer Relations Team is available to help you at 1-800-434-4246 (Monday - Friday). Thank you for your Patience and understanding.";

// set whether a page is displayed before the contact us page that encourages
// the user to check resources on the site before they send a question.
$_HCG_GLOBAL['display_mail_preface'] = true;

// Determine what site is calling this file. Each site using hcgPublic, needs
// an entry below.

$_HCG_GLOBAL['site_codes'] = array (
   'dev.example.com'               => array('ex','exdocs','dev','en_US'),
   'stage.example.com'          => array('ex','exstage','stage','en_US'),
   'example.com'                => array('ex','exdocs','live','en_US'),
);

// This test makes it so that I don't have to specify the www above
if (preg_match("/www./", $_SERVER['HTTP_HOST'])) {
   $domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
} else {
   $domain = $_SERVER['HTTP_HOST'];
}
$_HCG_GLOBAL['site_id'] = $_HCG_GLOBAL['site_codes'][$domain][0];
$_HCG_GLOBAL['doc_root_base'] = $_HCG_GLOBAL['site_codes'][$domain][1];
$_HCG_GLOBAL['server_level'] = $_HCG_GLOBAL['site_codes'][$domain][2];
$_HCG_GLOBAL['default_lang'] = $_HCG_GLOBAL['site_codes'][$domain][3];

if (empty($_HCG_GLOBAL['site_id'])) { // assign default site
   $_HCG_GLOBAL['site_id'] = 'ex';
   $_HCG_GLOBAL['doc_root_base'] = 'exdocs';
}

// set the BaseURL variable for use on pages where we want the absolute URL
if (preg_match('/^HTTPS/i', $_SERVER['SERVER_PROTOCOL'])) {
    $_HCG_GLOBAL['protocol'] = 'https://';
} else {
    $_HCG_GLOBAL['protocol'] = 'http://';
}
$_HCG_GLOBAL['baseURL'] = $_HCG_GLOBAL['protocol'].$_SERVER['HTTP_HOST'];


// set default paths to key areas

$_HCG_GLOBAL['application_dir'] = str_replace('/lib/config.inc.php', '', __FILE__);;
$_HCG_GLOBAL['lib_dir'] = $_HCG_GLOBAL['application_dir'] . "/lib";
$_HCG_GLOBAL['classes_dir'] = $_HCG_GLOBAL['lib_dir'] . "/classes";
$_HCG_GLOBAL['js_dir'] = $_HCG_GLOBAL['lib_dir'] . "/js";
$_HCG_GLOBAL['template_dir'] = $_HCG_GLOBAL['lib_dir'] . "/templates";
$_HCG_GLOBAL['forms_dir'] = $_HCG_GLOBAL['lib_dir'] . "/forms";
$_HCG_GLOBAL['hcg_classes_dir'] = $_HCG_GLOBAL['classes_dir'] . "/hcg_public";
$_HCG_GLOBAL['doc_root_dir'] = $_HCG_GLOBAL['application_dir'] . "/" .
   $_HCG_GLOBAL['doc_root_base'];

// set locations for 3rd Party classes

$_HCG_GLOBAL['adodb_dir'] = $_HCG_GLOBAL['classes_dir'] . "/adodb";
$_HCG_GLOBAL['formsess_dir'] = $_HCG_GLOBAL['classes_dir'] . "/formsess";
$_HCG_GLOBAL['pear_dir'] = $_HCG_GLOBAL['classes_dir'] . "/pear";
$_HCG_GLOBAL['smarty_dir'] = $_HCG_GLOBAL['classes_dir'] . "/smarty/libs";
$_HCG_GLOBAL['ewiki_dir'] = $_HCG_GLOBAL['classes_dir'] . "/ewiki-R1.01d4";
$_HCG_GLOBAL['ewiki2_dir'] = $_HCG_GLOBAL['classes_dir'] . "/ewiki-R1.02b";
$_HCG_GLOBAL['stocks_dir'] = $_HCG_GLOBAL['classes_dir'] . "/stocks";
$_HCG_GLOBAL['ziplocator_dir'] = $_HCG_GLOBAL['classes_dir'] . "/phpZipLocator";
$_HCG_GLOBAL['formsmgr_dir'] = $_HCG_GLOBAL['classes_dir'] . "/formsgeneration";
$_HCG_GLOBAL['htdig_dir'] = $_HCG_GLOBAL['classes_dir'] . "/htdiginterface";
$_HCG_GLOBAL['patuser_dir'] = $_HCG_GLOBAL['classes_dir'] . "/patUser";

// local folder settings
// these can be overridden using a local config file

$_HCG_GLOBAL['local_tpl_dir'] = "templates";
$_HCG_GLOBAL['local_js_dir'] = "js";

// support application settings

$_HCG_GLOBAL['convert_cmd'] = "/usr/local/bin/convert"; //ImageMagick convert
$_HCG_GLOBAL['sendmail'] = "";

// Proxy settings. If your system requires a proxy to access outside websites,
// enter the information here. If proxy setting are different for each server
// level, you can make these conditional using $_HCG_GLOBAL['server_level']

$_HCG_GLOBAL['proxy'] = "";
$_HCG_GLOBAL['proxy_port'] = "";

// set the database configuration. If database configurations are different 
// for each server level, you can make these conditional using
// $_HCG_GLOBAL['server_level']

$_HCG_GLOBAL["db"]["hcg_public_production"] = array
(
   "type" => "mysqli",
   "host" => "mysql-serv-master",
   "port" => "3307",
   "name" => "hcgpublic_live",
   "user" => "public_db",
   "pass" => "xxxxx",
);

// Turn on strict error reporting for debugging
if ($_HCG_GLOBAL['server_level'] == 'local' || $_HCG_GLOBAL['server_level'] == 'dev')
{
   error_reporting(E_ALL);
   ini_set('display_errors', TRUE);
   ini_set('display_startup_errors', TRUE);
}

if ($_HCG_GLOBAL['server_level'] == "live") {

   $_HCG_GLOBAL["db"]["hcg_public"] = array
   (
     "type" => "mysqli",
     "host" => "mysql-serv-master",
     "port" => "3307",
     "name" => "hcgpublic_live",
     "user" => "public_db",
     "pass" => "xxxxx",
   );

   $_HCG_GLOBAL["db"]["sessions"] = array
   (
     "type" => "mysqli",
     "host" => "mysql-serv-master",
     "port" => "3307",
     "name" => "sessiondb",
     "user" => "session",
     "pass" => "xxxxx",
   );

} elseif ($_HCG_GLOBAL['server_level'] == "stage") {

   $_HCG_GLOBAL["db"]["hcg_public"] = array
   (
     "type" => "mysqli",
     "host" => "mysql-serv-master",
     "port" => "3307",
     "name" => "hcgpublic_stage",
     "user" => "public_db",
     "pass" => "jage0katt",
   );

   $_HCG_GLOBAL["db"]["sessions"] = array
   (
     "type" => "mysqli",
     "host" => "mysql-serv-master",
     "port" => "3307",
     "name" => "sessiondb",
     "user" => "session",
     "pass" => "xxxxx",
   );

} elseif ($_HCG_GLOBAL['server_level'] == "dev") {

   $_HCG_GLOBAL["db"]["hcg_public"] = array
   (
     "type" => "mysqli",
     "host" => "bolwebdev1",
     "port" => "3306",
     "name" => "hcg_public",
     "user" => "public_db",
     "pass" => "xxxxx",
   );

   $_HCG_GLOBAL["db"]["sessions"] = array
   (
     "type" => "mysqli",
     "host" => "bolwebdev1",
     "port" => "3306",
     "name" => "sessiondb",
     "user" => "session",
     "pass" => "xxxxx",
   );

} else {  // server_level == "local"

   $_HCG_GLOBAL["db"]["hcg_public"] = array
   (
     "type" => "mysqli",
     "host" => "bolwebdev1",
     "port" => "3306",
     "name" => "hcg_public",
     "user" => "public_db",
     "pass" => "jage0katt",
   );

   $_HCG_GLOBAL["db"]["hcg_public_master"] = array
   (
     "type" => "mysqli",
     "host" => "bolwebdev1",
     "port" => "3306",
     "name" => "hcg_public",
     "user" => "public_db",
     "pass" => "jage0katt",
   );
   
   $_HCG_GLOBAL["db"]["sessions"] = array
   (
     "type" => "mysqli",
     "host" => "bolwebdev1",
     "port" => "3306",
     "name" => "sessiondb",
     "user" => "session",
     "pass" => "sally!linus",
   );

}

$_HCG_GLOBAL["logfile"] = $_HCG_GLOBAL['application_dir'] . "/syslog.txt";

$_HCG_GLOBAL["SAVE_SESSIONS_IN"] = "adodb_normal";


//############### should not have to edit below this line ###############

if ($_HCG_GLOBAL["SAVE_SESSIONS_IN"] == "adodb_normal") {
   require_once($_HCG_GLOBAL['adodb_dir']."/adodb.inc.php");
   $ADODB_SESSION_DRIVER  = $_HCG_GLOBAL["db"]["sessions"]['type'];
   $ADODB_SESSION_CONNECT = $_HCG_GLOBAL["db"]["sessions"]['host'];
   $ADODB_SESSION_USER    = $_HCG_GLOBAL["db"]["sessions"]['user'];
   $ADODB_SESSION_PWD     = $_HCG_GLOBAL["db"]["sessions"]['pass'];
   $ADODB_SESSION_DB      = $_HCG_GLOBAL["db"]["sessions"]['name'];
   require_once($_HCG_GLOBAL['adodb_dir']."/session/adodb-session2.php");
} elseif ($_HCG_GLOBAL["SAVE_SESSIONS_IN"] == "adodb_encrypted") {
   require_once($_HCG_GLOBAL['adodb_dir']."/adodb.inc.php");
   $ADODB_SESSION_DRIVER  = $_HCG_GLOBAL["db"]["sessions"]['type'];
   $ADODB_SESSION_CONNECT = $_HCG_GLOBAL["db"]["sessions"]['host'];
   $ADODB_SESSION_USER    = $_HCG_GLOBAL["db"]["sessions"]['user'];
   $ADODB_SESSION_PWD     = $_HCG_GLOBAL["db"]["sessions"]['pass'];
   $ADODB_SESSION_DB      = $_HCG_GLOBAL["db"]["sessions"]['name'];
   require_once($_HCG_GLOBAL['adodb_dir']."/session/adodb-cryptsession2.php");
}

// set the cookie so it looks like the cookie from the main intranet site
if ($intranet == TRUE && $_HCG_GLOBAL['server_level'] == 'live')
   session_set_cookie_params(0, "/", ".ctea.com");

session_start();

// do garbage collection explicitly
if ($_HCG_GLOBAL["SAVE_SESSIONS_IN"] == "adodb_normal" || $_HCG_GLOBAL["SAVE_SESSIONS_IN"] == "adodb_encrypted") {
   ADODB_Session::gc(ini_get("session.gc_maxlifetime"));
}

// grab any variables from post and get. These are extracted for easy access
// from the main page and also saved to a global variable that can be 
// extracted within a function.

// These extractions essentially do the same as register_globals being turned
// on. I would like to remove this eventually, but I will need to go through
// all files and make sure they're not dependiing on this.
	
$_HCG_GLOBAL['post_vars'] = array();
$_HCG_GLOBAL['get_vars'] = array();

if (!empty($_GET)) {
   extract($_GET, EXTR_OVERWRITE);
   $_HCG_GLOBAL['get_vars'] = $_GET;
} else if (!empty($HTTP_GET_VARS)) {
   extract($HTTP_GET_VARS, EXTR_OVERWRITE);
   $_HCG_GLOBAL['get_vars'] = $HTTP_GET_VARS;
} 

if (!empty($_POST)) {
   extract($_POST, EXTR_OVERWRITE);
   $_HCG_GLOBAL['post_vars'] = $_POST;
} else if (!empty($HTTP_POST_VARS)) {
   extract($HTTP_POST_VARS, EXTR_OVERWRITE);
   $_HCG_GLOBAL['post_vars'] = $HTTP_POST_VARS;
}

// initialize some variables
$_HCG_GLOBAL['javascript'] = '';

$_HCG_GLOBAL['passed_vars'] = array_merge($_HCG_GLOBAL['get_vars'], $_HCG_GLOBAL['post_vars']);

if (!empty($_SERVER) && isset($_SERVER['PHP_SELF'])) {
	$_HCG_GLOBAL['php_self'] = $_SERVER['PHP_SELF'];
} else if (!empty($HTTP_SERVER_VARS) && isset($HTTP_SERVER_VARS['PHP_SELF'])) {
	$_HCG_GLOBAL['php_self'] = $HTTP_SERVER_VARS['PHP_SELF'];
}

// reset $_SESSION['last_page'] and $_SESSION['this_page'] session variables
// This is not really used. I may want to remove it.

if (isset($_SESSION['this_page'])) {
   if ($_SESSION['this_page'] != $_HCG_GLOBAL['php_self']) {  // page is not being refreshed
      $_SESSION['last_page'] = $_SESSION['this_page'];
      $_SESSION['this_page'] = $_HCG_GLOBAL['php_self'];
   }
} else {
   $_SESSION['last_page'] = "";
   $_SESSION['this_page'] = $_HCG_GLOBAL['php_self'];
}

// added info in an attempt to find error with sessions & replication
if ( ! isset($_SESSION['this_site']))
   $_SESSION['this_site'] = $_HCG_GLOBAL['site_id'];

// set up $user_last_action session variable. This can be used by forms
// to determine if a form is being submitted more than once.

if (!isset($_SESSION['user_last_action'])) {
   $_SESSION['user_last_action'] = 100;
}

// set up the $_SESSION['language'] variable and load in any strings file
// if it exists.

if (!isset($_SESSION['language'])) {
   $_SESSION['language'] = $_HCG_GLOBAL['default_lang'];
}
$hcg_str_file = $_HCG_GLOBAL['doc_root_dir'] . "/inc/lang/" . strtolower($_SESSION['language']) . "-strings.php";
if (file_exists($hcg_str_file)) {
   require $hcg_str_file;
}
$_HCG_GLOBAL['str'] = isset($lang_strings) ? $lang_strings : '';

// set the include path, so I don't have to modify the php.ini file. The
// path to the 'lib_dir" has to be set in php.ini before or this file
// won't be found.
$hcg_include_path = $_HCG_GLOBAL['pear_dir']. ":" .
                    $_HCG_GLOBAL['hcg_classes_dir']. ":".
                    ini_get('include_path');

ini_set("include_path",$hcg_include_path);

// Turn off Register Globals
ini_set("register_globals",0);

// include any site-specific config file
$site_config = $_HCG_GLOBAL['doc_root_dir'] . "/inc/" . $_HCG_GLOBAL['site_id'] . "_config.inc.php";
if (file_exists($site_config)) {
   include $site_config;
}


// -------------------------------------------------------------------------
// TAG: function get()
//   This is the primary tag function. All other tags are called from this
//   one in order to simplify the main pages by making it so the page
//   doesn't have to include the function library it's using.
//
// -------------------------------------------------------------------------
function get()
{
   // get arguments and separate into action and parameters
   $params = array();
   $num_params = func_num_args();
   if ($num_params > 0) {
      $hcgAction = func_get_arg(0);
      if ($num_params > 1) {
         for ($i = 1; $i < $num_params; $i++) {
            $params[$i-1] = func_get_arg($i);
         }
      }
   } else {
      die("get() function requires at least one parameter.");
   }
   
   // break $hcgFunction into $module and $action
   list($module, $action) = explode(".", $hcgAction);
   
   require_once("mod_" . $module . "/" . $module . ".fns.php");
   
   if (is_callable($action)) {
      $result = call_user_func_array($action, $params);
   }
   
   return $result;
}


// -------------------------------------------------------------------------
// TAG: function getAdm()
//   This is the primary admin tag function. All other tags are called from 
//   this one in order to simplify the main pages by making it so the page
//   doesn't have to include the function library it's using.
//
// -------------------------------------------------------------------------
function getAdm()
{
   // get arguments and separate into action and parameters
   $params = array();
   $num_params = func_num_args();
   if ($num_params > 0) {
      $hcgAction = func_get_arg(0);
      if ($num_params > 1) {
         for ($i = 1; $i < $num_params; $i++) {
            $params[$i-1] = func_get_arg($i);
         }
      }
   } else {
      die("get() function requires at least one parameter.");
   }
   
   // break $hcgFunction into $module and $action
   list($module, $action) = explode(".", $hcgAction);
   
   require_once("mod_" . $module . "/" . $module . ".adm.php");
   
   if (is_callable($action)) {
      $result = call_user_func_array($action, $params);
   }
   
   return $result;
}


?>
