<?php

// This is a kind of extension of the ADODB package. It allows me 
// to use the database information stored in hcgPublic's 
// config.inc.php file while still returning a valid ADODB object.

// I think this is the place to put failover so that if one of the DB
// servers isn't available, it automatically tries the other.
// start here: http://codewalkers.com/seecode/47.html

global $_HCG_GLOBAL;
require_once($_HCG_GLOBAL['adodb_dir']."/adodb.inc.php");

   function &HCGNewConnection($db, $persist=false)
   {
      global $_HCG_GLOBAL;
      
      $db_type = $_HCG_GLOBAL["db"][$db]["type"];
      $db_host = $_HCG_GLOBAL["db"][$db]["host"];
      $db_user = $_HCG_GLOBAL["db"][$db]["user"];
      $db_pass = $_HCG_GLOBAL["db"][$db]["pass"];
      $db_name = $_HCG_GLOBAL["db"][$db]["name"];
      $db_port = $_HCG_GLOBAL["db"][$db]["port"];
      if (isset($_HCG_GLOBAL["db"][$db]["dsn"]))
      {
         $db_dsn = $_HCG_GLOBAL["db"][$db]["dsn"];
      }

      
      if ($db_type == 'odbc_mssql')
      {
         $obj = ADONewConnection($db_dsn);
//         $obj->debug = TRUE;
//         echo '<pre>'; print_r($obj); echo '</pre>'; exit;
      }
      else
      {
         $obj = ADONewConnection($db_type);
         $obj->port = $db_port;
         if ($persist) {
            $status = $obj->PConnect($db_host, $db_user, $db_pass, $db_name);
         } else {
            $status = $obj->Connect($db_host, $db_user, $db_pass, $db_name);
         }
      }
      $obj->setFetchMode(ADODB_FETCH_NUM);

      return $obj;
   }
	
?>