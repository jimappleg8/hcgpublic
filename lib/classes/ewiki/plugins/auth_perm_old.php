<?php

/*
   This auth/permission plugin wraps the two older configuration constants
   into the newer auth pluginterface. You shoul however prefer to drop it!
*/

//-- overwriting of read-only pages
define("EWIKI_ALLOW_OVERWRITE", 0);

//-- allow to edit a page (if all were locked)
define("EWIKI_EDIT_AUTHENTICATE", 0);


$ewiki_plugins["auth_perm"][0] = "ewiki_auth_perm_old";

function ewiki_auth_perm_old($id, &$data, $action, &$ring) {

   global $ewiki_author;

   if (true) {
      $ring = 3;
   }

   if (EWIKI_EDIT_AUTHENTICATE) {
      if (empty($ewiki_author)) {
         $ring = 3;
      }
      else {
         $ring = 2;
      }
   }

   if (EWIKI_ALLOW_OVERWRITE) {
      $ring = 1;
   }

}

?>