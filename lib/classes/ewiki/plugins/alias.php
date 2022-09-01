<?php

#  use this plugin to map PageAliases to existing WikiPages
#
#  it however only works/patches the page format function, and does
#  not apply if you use the ewiki lib only partially
#
#  if you add an alias for a page, it's assumed to exist


$ewiki_plugins["alias"] = array(
   "FrontPage" => "ErfurtWiki",
   "WikiInfo" => "AboutPlugins",
   "PageAlias" => "RealName",
// ...
);


$ewiki_plugins["format_source"] = "ewiki_page_aliases";


function ewiki_page_aliases(&$src) {
   global $ewiki_links, $ewiki_plugins;
   $ewiki_links = array_merge(
      $ewiki_links,
      $ewiki_plugins["alias"]
   );
}

foreach ($ewiki_plugins["alias"] as $page=>$uu) {
   $ewiki_plugins["page"][$page] = "ewiki_page_alias";
}

function ewiki_page_alias($id, $data, $action) {
   global $ewiki_plugins;
   return(ewiki_page($ewiki_plugins["alias"][$id]));
}


?>