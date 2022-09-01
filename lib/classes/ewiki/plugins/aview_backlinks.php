<?php

#
#  this plugin prints the "pages linking to" below a page (the same
#  information the "links/" action does)
#


$ewiki_plugins["view_append"][] = "ewiki_view_append_backlinks";


function ewiki_view_append_backlinks($id, $data, $action) {

   $result = ewiki_database("SEARCH", array("refs" => $id));
   $pages = array();

   while ($r = $result->get()) {
      if ( strpos("\n\n\n\n".$r["refs"]."\n\n\n\n", "\n$id\n") ) {
         $pages[] = $r["id"];
      }
   }

   $o="";
   foreach ($pages as $id) {
      $o .= ' <a href="'.ewiki_script("",$id).'">'.$id.'</a>';
   }
   ($o) && ($o = "<div class=\"wiki_backlinks\"><small>Backlinks:</small><br>$o</div>\n");

   return($o);
}


?>