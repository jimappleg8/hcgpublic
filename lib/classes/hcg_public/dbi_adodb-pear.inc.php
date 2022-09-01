<?php

// This is a kind of extension of the ADODB package. It allows me 
// to use the database information stored in hcgPublic's 
// config.inc.php file while still returning a valid ADODB object.

global $_HCG_GLOBAL;
require_once($_HCG_GLOBAL['adodb_dir']."/adodb-pear.inc.php");

   function &HCGNewPEARConnection($db, $persist=true)
   {
      GLOBAL $_HCG_GLOBAL;
      
      $db_type = $_HCG_GLOBAL["db"][$db]["type"];
      $db_host = $_HCG_GLOBAL["db"][$db]["host"];
      $db_user = $_HCG_GLOBAL["db"][$db]["user"];
      $db_pass = $_HCG_GLOBAL["db"][$db]["pass"];
      $db_name = $_HCG_GLOBAL["db"][$db]["name"];

      $obj = ADONewConnection($db_type);
      
      if ($persist) {
         $status = $obj->PConnect($db_host, $db_user, $db_pass, $db_name);
      } else {
         $status = $obj->Connect($db_host, $db_user, $db_pass, $db_name);
      }
      $obj->setFetchMode(ADODB_FETCH_NUM);
		
      return $obj;
   }
	
?>