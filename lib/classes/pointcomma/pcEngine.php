<?php
/**
 * PointComma - Launch the framework lib - application_head.php
 * 
 * Needed header in every file that try to use the framework
 * 
 * Can be customized/optimized depending on the project to add extra lib.
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * 
 */

//
//
// WARNING !!!!!!!! Configuration file must be loaded
// 
//

//Check if sessions are still supported
function_exists('session_start') or die("Session are not supported in this version of PHP, Please contact Your hosting provider");

/////////////////////////////////////
//
// LOAD FRAMEWORK LIB
//
/////////////////////////////////////
  
//disable assertions by default DO NOT REMOVE IT !!!!!!
assert_options(ASSERT_ACTIVE, 0);

//MAKE SURE that magic quotes GPC is DISABLED
//a .htaccess is provided with pointcoma to disable magic quote GPC 
//the following code could be used if the .htaccess does not work on the hosting provider

/*
if (get_magic_quotes_gpc()) {
   function stripslashes_deep($value)
   {
       $value = is_array($value) ?
                   array_map('stripslashes_deep', $value) :
                   stripslashes($value);

       return $value;
   }

   $_POST = array_map('stripslashes_deep', $_POST);
   $_GET = array_map('stripslashes_deep', $_GET);
   $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
*/

//WARNING to avoid error after installation (do not forget to install smarty lib in a folder call smarty and located in the class folder and to create in it the folder templates_c in its directory)

//load template engine class if it has not been done previously
//require_once($pcConfig['includePath'].$pcConfig['classFolder'].'pctemplate.php');
require_once 'template.class.php';
 
//Message Stack Class
require($pcConfig['includePath'].$pcConfig['classFolder'].'pcmessage_stack.php');
  
// Localization messages
require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcmessages-en.inc.php');

// DB abstraction layer
require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcdb_mysql.inc.php');
  
//Custom Error management
require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcerror.php');
  
if ($pcConfig['mysqlSessions']) {
  // Mysql Session lib
  require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcsession.php');
}
  
// Right Management
require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcright.php');

// Output content Management
require($pcConfig['includePath'].$pcConfig['functionFolder'].'pcoutput.php');

//-------------------------------- Lib Loaded


/////////////////////////////////////
//
// Initialize PointComma
//
/////////////////////////////////////

//initiate DB connection
if (!pcdb_connect($pcConfig['db']['host'], $pcConfig['db']['login'], $pcConfig['db']['pass'], $pcConfig['db']['name'])) {
  //unable to connect
}

//Security routine: remove DB information
unset($pcConfig['db']);

if (isset($pcDynamicPage)) {
  header ('Expires: Mon, 1 Jan 2001 01:00:00 GMT');
  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
  header ('Cache-Control: no-cache, must-revalidate');
  header ('Pragma: no-cache');
}

//Load/Start session
session_name('pcAdminId');
session_start();

//-------------------------------- Pointcomma Initialized

/////////////////////////////////////
//
//  INITIALIZE DEBUG ENGINE
//
/////////////////////////////////////


//initialize or get the manual debugging flag
//$_SESSION['pcDebugDisplay']  = pcDefaultValue('bool',false,'pcDebugDisplay', 'S');

//determine the debug status
$pcConfig['debug']['active'] = (isset($_SESSION['pcDebugDisplay']))?$_SESSION['pcDebugDisplay']  or $pcConfig['debug']['active']:$pcConfig['debug']['active'];

//Debbuging routine
//Need to be launch just after the configuration
if ($pcConfig['debug']['active'] ) {
  require_once($pcConfig['includePath'].$pcConfig['functionFolder'].'pcdebug.php');
}
//-------------------------------- Debug engine initialized

assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),0, 'Start Authentification Procedure',0)");

/////////////////////////////////////
//
// Authentication procedure
//
/////////////////////////////////////

$pcLoginUserName = pcDefaultValue('string','','pcLoginUserName', 'AGP');
$pcLoginPassword = pcDefaultValue('string','','pcLoginPassword', 'AGP');

if ((isset($pcLoginUserName) && isset($pcLoginPassword) && !empty($pcLoginUserName)) || ($pcLoginUserName=='logout')) {
  // pcLoginUserName exists, and is true and not "logout"
  // if user login request
  if ($pcLoginUserName == 'logout') {
    //logout user
    trigger_error('You have been logged out',WARNING);
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'logoutAttempt',2)");
    pcLogout();
    if ($pcConfig['anonymousLogin']) {
      pcLogin('anonymous');
    }
  } 
  else {
    //try to login the user
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'loginAttempt',2)");
    pcLogin($pcLoginUserName,$pcLoginPassword);
    
  }  
} elseif (!pcIsLogged() and pcIsLogged()!=='') {
  //if no login credentials provided and user session doesn't exist: don't do anything
    // if anonymous login authorized login anonymous
  if ($pcConfig['anonymousLogin']) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'User Not logged, AnonymousloginAttempt',2)");
    pcLogin('anonymous');
  } else {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'User Not logged and Anonymous login forbidden',3)"); 
  }
  
} elseif (isset($_SESSION['clearance']['generatedOn']) and ($_SESSION['clearance']['generatedOn'] < getGlobal('lastAdminUpdateOn'))) {
  //login credentials not provided, user session exists: user is logged in. Recent admin update.
  
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Logged in again because of admin update',5)");
  //reload clearance
  pcRegenerateClearance();

} else {
  // the fact that this comes up means that there's something really not good
  // and what is worse, this comes up anytime there is no login or logout action
  // meaning it's the normal state the page is in
  
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'UseAlreadyLogged',3,'User '.\$_SESSION['clearance']['userName'].' already logged')");
}

if (!defined('CLEARANCE')) {
  define('CLEARANCE', serialize($_SESSION['clearance']));
  //serial clearance array and store it as a constant 
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Set CLEARANCE constant',1)");
} else {
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Fatal error: authentication failed',9,'CLEARANCE already defined')");
}

//-------------------------------- End of Identification

// i/o functions

$pcColumns['chars']['i'] = 'pcElement';
$pcColumns['chars']['s'] = 'strval';
$pcColumns['chars']['d'] = 'dateval';
$pcColumns['chars']['n'] = 'numval';
$pcColumns['chars']['t'] = 'txtval';
$pcColumns['chars']['f'] = 'xfile';
$pcColumns['chars']['b'] = 'numval';
$pcColumns['chars']['l'] = 'numval';
$pcColumns['chars']['u'] = 'strval';

$pcColumns['charTypes']['i'] = 'mediumint(8)';
$pcColumns['charTypes']['s'] = 'varchar(255)';
$pcColumns['charTypes']['d'] = 'datetime';
$pcColumns['charTypes']['n'] = 'float(10,2)';
$pcColumns['charTypes']['t'] = 'text';
$pcColumns['charTypes']['f'] = 'varchar(120)';
$pcColumns['charTypes']['b'] = 'smallint';
$pcColumns['charTypes']['l'] = 'smallint';
$pcColumns['charTypes']['u'] = 'varchar(12)';

$pcColumns['charModifier']['i'] = '';
$pcColumns['charModifier']['s'] = '';
$pcColumns['charModifier']['d'] = '';
$pcColumns['charModifier']['n'] = '';
$pcColumns['charModifier']['t'] = '';
$pcColumns['charModifier']['f'] = '';
$pcColumns['charModifier']['b'] = ' DEFAULT 0 NOT NULL';
$pcColumns['charModifier']['l'] = '';
$pcColumns['charModifier']['u'] = '';


// all possible columns for an Xval
$pcColumns['xvals'] = array('pcElement', 'strval', 'dateval', 'numval', 'txtval', 'xfile');

$pcColumns['labels'] = array(
  'chars' => array(
    'i' => 'One item',
    's' => 'String',
    'd' => 'Date',
    'n' => 'Number',
    't' => 'Text',
    'f' => 'File',
    'b' => 'Boolean',
    'l' => 'List',
    'u' => 'User',
  )
);

?>