<?php
/*
   This plugin allows to use an existing SQL database table with usernames
   and passwords for authentication purposes. You can use MySQL or the
   anydb backend.

   Prior usage you must configure, which database table and field names
   to retrieve the user information from. Also for some existing systems
   it was possible to substitute the "$ewiki_ring" setting from (to set
   admin priviliges).

   You also need:
   + EWIKI_PROTECTED_MODE=1
   + one auth_perm_* plugin
   + one auth_method_* plugin
*/



$ewiki_plugins["auth_userdb"] = "ewiki_auth_userdb_sql";


function ewiki_auth_userdb_sql($username) {

   #-- configure
   $sql_query = "mysql_query";
   $sql_fetch = "mysql_fetch_array";
#   $sql_query = "anydb_query";
#   $sql_fetch = "anydb_fetch_array";

   #-- which table and row names
   $TABLE = "users";
   $ROW_USER = "name";
   $ROW_PW = "password";
  //$ROW_PRIV = "privilege";
  //$MATCH_PRIV = array("user"=>2, "moderator"=>1, "admin"=>0);

   # PostNuke
  //$TABLE="nuke_users";  $ROW_USER="pn_name";  $ROW_PW="pn_pass";
  //$ROW_PRIV="";
   # pSlash
  //$TABLE="ps_users";  $ROW_USER="uname";  $ROW_PW="pass";
  //$ROW_PRIV="status";  $MATCH_PRIV("member"=>2, "Admin"=>0);
   # coWiki
  //$TABLE="cowiki_user";  $ROW_USER="name";  $ROW_PW="passwd";
  //$ROW_PRIV="";
   # e107
  //$TABLE="user";  $ROW_USER="user_name";  $ROW_PW="user_password";
  //$ROW_PRIV="user_admin";  $MATCH_PRIV(0=>2, 1=>0);
   # Geeklog
  //$TABLE="gl_user";  $ROW_USER="username";  $ROW_PW="passwd";
  //$ROW_PRIV="";


   #-- proceed
   $ret = array();

   $username = addslashes($username);
   if ($result = $sql_query("select $ROW_PW from $TABLE where $ROW_USER='$username'") && ($row = $sql_fetch($result))) {

      $ret[0] = $row[$ROW_PW];
      $ret[1] = 2;  // default ring level

      if ($ROW_PRIV && ($result = $sql_query("select $ROW_PRIV from $TABLE where $ROW_USER='$username'")) && ($row = $sql_fetch($result))) {
         if (($o = $row[$ROW_PRIV]) && isset($MATCH_PRIV[$i])) {
            $ret[1] = $MATCH_PRIV[$i];
         }
      }
   }

   #-- done
   return($ret);
}


?>