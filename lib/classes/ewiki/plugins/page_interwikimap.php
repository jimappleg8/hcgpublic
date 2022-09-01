<?php

# prints out the list of known InterWiki:ShortCuts
# (using a <dl>)


$ewiki_plugins["page"]["InterWikiMap"] = "ewiki_page_interwikimap";


function ewiki_page_interwikimap($id, $data, $action) {

   global $ewiki_plugins;

   $o .= "<h2>$id</h2>\n";

   $o .= '<dl id="InterWikiMap">'."\n";
   foreach ($ewiki_plugins as $shortcut=>$url) {
      $o .= "<dt>$shortcut:</dt>\n".
           "   <dd><a href=\"$url\">$url</a></dd>\n";
   }
   $o .= "</dl>";
}

?>