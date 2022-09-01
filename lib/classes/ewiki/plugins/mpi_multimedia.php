<?php

 # this mpi allows to link multimedia files using following syntax:
 # <plugin: multimedia http://www.example.com/movie.swf>


$ewiki_plugins["mpi"]["multimedia"] = "ewiki_mpi_multimedia";


function ewiki_mpi_multimedia($action="html", $args=array()) {

   switch ($action) {
      case "doc": return("The <b>multimedia</b> plugin allows to reference multimedia objects which are no plain images (like videos, flash, applets).");
      case "desc": return("reference multimedia files");

      default:
         $href = implode("", array_keys(array_shift($args)));
         $o .= '<object data="' . $href . '">';
         foreach ($args as $i=>$v) {
            $o .= '<param name="'.$i.'" value="'.htmlentities($v).'">';
         }
         $o .= "Your browser cannot view this multimedia object.";
#<off>#  $o .= '<embed src="' . $href . '"></embed>';
         $o .= "</object>";
   }

   return($o);
}

?>