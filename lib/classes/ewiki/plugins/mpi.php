<?php

 # this plugin interfaces ewiki to the markup plugins initially developed
 # by Hans B Pufal (http://www.aconit.org/hbp/CCC/Ewiki/index.php)
 #
 # the term "mpi" just means "markup plugins` interface"
 #
 # from now on, the plugins (mpi_*.php) are only loaded on demand,
 # you can however disable this behaviour for security reasons:

 define("EWIKI_MPI_DEMANDLOAD", 1);
 define("EWIKI_MPI_AUTOLOAD_DIR", dirname(__FILE__));

 define("EWIKI_MPI_FILE", "mpi_");       # better do not change
 define("EWIKI_MPI_MARKUP_REGEX", "/&lt;\\??(plugin:|e?wiki:|mpi:|plugin(?:-link|-form|-doc|)\s)\s*(.*?)\\??&gt;/i");



 #-- register at ewiki pluginterface
 $ewiki_plugins["format_final"][] = "ewiki_mpi_handler";
 $ewiki_plugins["action"]["mpi"] = "ewiki_mpi_action";






 #-- called from inside ewiki_format() engine
 function ewiki_mpi_handler(&$html) {

    $html = preg_replace_callback(EWIKI_MPI_MARKUP_REGEX, "ewiki_mpi_regex_callback", $html);

 }




 #-- regex callback to activate the inline plugin
 function ewiki_mpi_regex_callback($uu) {

    global $ewiki_plugins;

    #-- get mpi invocation string
    @list($uu_string, $uu_code, $uu_decode2) = $uu;

    #-- extract/correct plugin name and args
    $uu_decode2 = preg_replace("/\??<[^>]+>/", "", $uu_decode2);
    preg_match('/([^:\s]+)(\s.*)?/', $uu_decode2, $uu);
    $mpi_name = strtolower($uu[1]);
    $uu_pf_args = $uu[2];

    #-- $mpi-action
    $mpi_action = "html";
    if ($p = strpos($uu_code, "-")) {
       $mpi_action = strtolower(substr($uu_code, ++$p));
    }

    #-- split args
    $args = array();
    foreach (explode(" ", $uu_pf_args) as $a) {
       $i = trim(trim(strtok($a, "=")), "|");
       $v = trim(strtok("\000"));
       $args[$i] = $v;
    }

    #-- plugin-link
    if ($mpi_action == "link") {
       return(ewiki_mpi_link($mpi_name, $pf_args));
    }

    #-- select plugin function
    $pf = $ewiki_plugins["mpi"][$mpi_name];

    #-- load plugin
    if (!function_exists($pf) && EWIKI_MPI_DEMANDLOAD) {

       $mpi_file = EWIKI_MPI_AUTOLOAD_DIR . "/" . EWIKI_MPI_FILE . strtolower($mpi_name) . ".php";
       @include($mpi_file);

       $pf = $ewiki_plugins["mpi"][$mpi_name];
    }

    #-- execute plugin
    if (function_exists($pf)) {
       $uu_string = $pf($mpi_action, $args);
    }
    else {
       $uu_string .= "<!-- referenced mpi not available -->";
    }

    return($uu_string);
}



function ewiki_mpi_link($mpi_name, $args) {

   $a = EWIKI_ADDPARAMDELIM;
   foreach ($args as $i => $v) {

      $a .= urlencode($i) . "=" . urlencode($v) . "&";
   }

   return('<a href="' . ewiki_script("mpi", $mpi_name, $a) . '">' . $mpi_name . '</a>' );
}



function ewiki_mpi_action($id, $data, $action) {

   global $ewiki_plugins;

   if (function_exists($pf = $ewiki_plugins["mpi"][strtolower($id)])) {

      return($pf("html", $_REQUEST));

   }

}



?>