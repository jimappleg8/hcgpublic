<?php

/*
  this authentication/permission plugin, implements a user database
  with an internal array (most simple approach)

  you can insert passwords in cleartext(), crypt(), md5() or sha1()
  privliege levels ("rings") are:
    0 - administrator rights
    1 - privileged user (advanced functionality: uploading)
    2 - standard services (edit, view, info, ...)
    3 - unprivileged users (only browsing),
        which is also default if not logged in (see EWIKI_AUTH_DEFAULT_RING)
  usernames and passwords are __always and everywhere__ case-sensitive!

  to use authentication you need also:
    - EWIKI_PROTECTED_MODE
    - plugins/auth_perm_ring.php
    - plugins/auth_method_http.php (or another one)
  other "auth_user" plugins can also be enabled
*/

#-- user names and passwords, ring_levels
$ewiki_auth_user_array = array_merge($ewiki_auth_user_array, array(
   # "username"	=> array("password", $RING_LEVEL=2),
   # "user2"	=> array("sU7oi30Zmf2KTr4", 1),
   "test" => array("test", 2),
));


#-- glue
$ewiki_plugins["auth_user"][] = "ewiki_auth_user_array";

#-- code
function ewiki_auth_user_array($username, $password) {

   global $ewiki_auth_user_array;

   if ($entry = $ewiki_auth_user_array[$username]) {
      $_pw = $entry[0];
      if ( ($_pw == $password)
           || ($_pw == md5($password))
           || ($_pw == crypt($password, substr($_pw[0], 0, 2)))
           || function_exists("sha1") && ($_pw == sha1($password))
      )
      {
         return(array($username, $entry[1]));
      }
      else {
         ewiki_log("auth_user_array: wrong password supplied for user '$username'", 3);
         return(false);
      }
   }
   else {
      return(false);
   }
}


?>