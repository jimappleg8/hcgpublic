<?php 

// =========================================================================
//  core.fns.php
//  written by Jim Applegate
//  last modified: 
// =========================================================================

require_once("template.class.php");
require_once("mod_core/core.inc.php");

// ------------------------------------------------------------------------
require_once("mod_db/db.fns.php");
// NOTE: The following functions were moved into the db.inc.php file
//   because they should be used within TAG functions and support functions
//   but should not be tags themselves. They are included in the above
//   call for compatibility reasons.
//     db_connect()
//     db_query()
//     db_query_array()
//     db_query_hash()
//     db_query_item()
//
// ------------------------------------------------------------------------




// ------------------------------------------------------------------------
// TAG: back_url
//   returns a URL pointing to the previous page
//
// ------------------------------------------------------------------------
function back_url()
{
   global $_HCG_GLOBAL;
   // return $_HCG_GLOBAL['php_referrer'];

}


// ------------------------------------------------------------------------
// TAG: append_js_file
//   sets the global $_HCG_GLOBAL['javascript'] variable by inserting the 
//   .js file specified. If Javascript is automatically generated, it can
//   be inserted into the same variable using append_js_code(). These
//   functions can be called from pages or from within scripts.
//
//   The function looks in the site folder/js/ for the file, and if it's 
//   not there, the file is assumed to be in the $_HCG_GLOBAL['js_dir']
//   directory.
//
// ------------------------------------------------------------------------
function append_js_file($js_file)
{
   global $_HCG_GLOBAL;
   
   $file_path = $_HCG_GLOBAL['application_dir']."/".$_HCG_GLOBAL['doc_root_base'] . "/js/" . $js_file;

   if (!file_exists($file_path))
   {
      $file_path = $_HCG_GLOBAL['js_dir']."/".$js_file;
   }
   
   $_HCG_GLOBAL['javascript'] .= "\n" . getFile2Str($file_path);
   
}


// ------------------------------------------------------------------------
// TAG: get_js_file
//   returns the contents of a javascript file. This is for cases when you
//   need to grab some JavaScript but you don't want it mixed with the 
//   JavaScript used in the Header section of the page. For example, if you
//   have a script that writes the call for a Flash image.
//
//   The function looks in the site folder/js/ for the file, and if it's 
//   not there, the file is assumed to be in the $_HCG_GLOBAL['js_dir']
//   directory.
//
// ------------------------------------------------------------------------
function get_js_file($js_file)
{
   global $_HCG_GLOBAL;
   
   $file_path = $_HCG_GLOBAL['application_dir']."/".$_HCG_GLOBAL['doc_root_base'] . "/js/" . $js_file;

   if (!file_exists($file_path)) {
      $file_path = $_HCG_GLOBAL['js_dir']."/".$js_file;
   }
   
   return getFile2Str($file_path);
   
}


// ------------------------------------------------------------------------
// TAG: append_js_code
//   sets the global $_HCG_GLOBAL['javascript'] variable by inserting the 
//   contents of the $js_code variable. This function can be called from
//   pages or from within scripts.
//
// ------------------------------------------------------------------------
function append_js_code($js_code)
{
   global $_HCG_GLOBAL;
   
   $_HCG_GLOBAL['javascript'] .= "\n" . $js_code;
   
}


// ------------------------------------------------------------------------
// TAG: wrap_js
//   wraps the contents of $_HCG_GLOBAL['javascript'] in the HTML code
//   given in the specified file. The default file is wrapper.js which
//   is a template file with the variable $hcg_javascript in the middle.
//
//   The function returns the contents of the $_HCG_GLOBAL['javascript']
//   wrapped in the wrapper file. This is so that in a PHP file, you can
//   assign the results to a header variable such as $hdr['js'] and pass
//   it to the header template.
//
//   The function looks in the site folder/js/ for the file, and if it's 
//   not there, the file is assumed to be in the $_HCG_GLOBAL['js_dir']
//   directory.
//
// ------------------------------------------------------------------------
/*
function wrap_js($js_file = "wrapper.js")
{
   global $_HCG_GLOBAL;
   
   $file_path = $_HCG_GLOBAL['application_dir']."/".$_HCG_GLOBAL['doc_root_base'] . "/js/" . $js_file;

   if (!file_exists($file_path)) {
      $file_path = $_HCG_GLOBAL['js_dir']."/".$js_file;
   }

   $t = new HCG_Smarty;
   $t->assign("hcg_javascript", $_HCG_GLOBAL['javascript']);

   return $t->fetch($file_path);
   
}
*/
// --------------------------------------------------------------------
   
/**
 * Return the JS collection wrapped in HTML JavaScript tags
 *
 * The default file is wrapper.js which is a view file with the 
 * variable $javascript in the middle.
 *
 * @access   public
 * @param    string   The wrapper file without the extension
 * @return   string
 */
function wrap_js($wrapper = "wrapper.js")
{
   global $_HCG_GLOBAL;
   
   if ($_HCG_GLOBAL['javascript'] != '')
   {
      $dir = "js/";
      $file = $wrapper;
      
      $file_path = $_HCG_GLOBAL['application_dir']."/".$_HCG_GLOBAL['doc_root_base'] . "/js/" . $file;

      if ( ! file_exists($file_path))
      {
         $file_path = $_HCG_GLOBAL['js_dir']."/".$file;
      }

      if ($file_path)
      {
         $results = read_file($file_path);
         $results = str_replace('{javascript}', $_HCG_GLOBAL['javascript'], $results);
         return $results;
      }
      else
      {
         return FALSE;
      }
   }      
   return $_HCG_GLOBAL['javascript'];
}


// ------------------------------------------------------------------------
//
// TAG: event()
//
// Record an event in the system log.
//
// desc - a description of the event.
//
// type - the type of event (debug, info, warning, error).
//
// ------------------------------------------------------------------------

function event($desc, $type = "info")
{
  global $_HCG_GLOBAL;

  # Save the (formatted) message in the log file.

  if(!($logfile = $_HCG_GLOBAL["logfile"])) return; # No log file defined!
  $message = date("Y-m-d H:i:s") . " $type : $desc";
  if(file_exists($logfile)) error_log($message . "\n", 3, $logfile);

  # If it's an error, we should kill the page and display a message to
  # the end user.

  if($type == "error")
  {
    if(preg_match("/Query/", $message) || preg_match("/Sybase/", $message))
      $nice = "There is a problem with the database or some program code," .
      " because a database query failed to execute properly.";

    else if(preg_match("/ for inclusion/", $message))
      $nice = "There is a problem with the program code, because it" .
      " is looking for a file which doesn't exist.";

    else
      $nice = "There may be a problem with the program code," .
      " the database may be corrupt, or the server may be" .
      " experiencing problems.";

    ob_end_clean();
    print <<< END
<html>               
<head>
<title>Application Error</title>
</head>                         

<body bgcolor="#ffcccc">

<table border="1" cellspacing="0" cellpadding="20" align="center"
  bgcolor="#ffffff">
<tr>
  <td>
    <font face="arial, helvetica, sans-serif">
    <h1>Application Error!</h1>

    <p>
    $nice
    </p>

    <p>
    If you have access to technical support for this site, please
    notify them of the following error:
    </p>

    <p><font color="#666666">
    $message
    </font></p>
    </font>
  </td>
</tr>
</table>

</body>
</html>
END;

    exit; # Die.

  } # Error?

}

?>
