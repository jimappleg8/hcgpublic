<?php

  # this is the configuration for the ewiki page and database tools
  #


  #-- most simple authentication:
  include("../fragments/auth.php");


  #-- normalize cwd (stupid approach)
  if (!file_exists($LIB="ewiki.php")) {
     chdir("..");
     define("EWIKI_SCRIPT", "../?");
     define("EWIKI_SCRIPT_BINARY", "../?binary=");
  }


  #-- open db connection, load 'lib'
  include("./config.php");

?>