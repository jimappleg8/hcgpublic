<?php

/*
   This auth method queries authentication data via the HTTP AUTH method.
   The advantage of this over other procedures is, that these authentication
   infos are usually forgotten as soon as the browser gets closed -- well
   that's at least how it should be, TCPA-enabled IEs may of course transmit
   authentication data to some third(?) party.

   you'll need:
    - EWIKI_PROTECTED_MODE
    - plugins/auth_perm_ring.php (or another one)
    - plugins/auth_user_array.php (or another one)
    - a binary-safe ewiki/yoursite setup  (see the
      section on uploads and images in the README)

   You can only load __one__ auth method plugin!

   Note: if your want to your wiki to be accessible to a small group of
   people only, then you should favour the http authentication mechanism
   of your webserver! This is just a very poor implementation of the HTTP
   BASIC AUTH scheme.
   (all in here borrowed from the fragments/auth.php)
*/


#-- glue
$ewiki_plugins["auth_query"][0] = "ewiki_auth_query_http";


#-- text data
$ewiki_t["en"]["RESTRICTED_ACCESS"] = "You must be authenticated to use this part of the wiki.";


#-- code
function ewiki_auth_query_http(&$output, &$ewiki_author, &$ewiki_ring, $force_query=0) {

   global $ewiki_plugins, $ewiki_author, $ewiki_ring;

   #-- fetch user:password
   if ($uu = trim($_SERVER["HTTP_AUTHORIZATION"])) {
      strtok($uu, " ");
      $uu = strtok(" ");
      $uu = base64_decode($uu);
      list($_a_u, $_a_p) = explode(":", $uu, 2);
   }
   elseif (strlen($_a_u = trim($_SERVER["PHP_AUTH_USER"]))) {
      $_a_p = trim($_SERVER["PHP_AUTH_PW"]);
   }

   #-- check password
   $_success=0;
   if (strlen($_a_u) && strlen($_a_p)) {
      foreach ($ewiki_plugins["auth_user"] as $pf) {
         if ($data = $pf($_a_u, $_a_p)) {
            $ewiki_author = $data[0];
            $ewiki_ring = min($data[1], $ewiki_ring);
            $_success=1;
         }
      }
   }

   #-- request HTTP Basic authentication otherwise
   if (!$_success && $force_query) {
      $output = ewiki_t("RESTRICTED_ACCESS");
      header('HTTP/1.1 401 Authentication Required');
      header('Status: 401 Authentication Required');
      header('WWW-Authenticate: Basic realm="'.$output.'"');
   }

   #-- fin
   return($_success);
}

?>