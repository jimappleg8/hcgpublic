<?php

/*
   dynamic plugin loading
   пппппппппппппппппппппп
   Will load plugins on demand, so they must not be included() one by one
   together with the core script. This is what commonly the "plugin idea"
   suggests, and only has minimal disadvantages.
   - This loader currently only handles "page" and "action" plugins,
     many other extensions must be activated as before (the other ones
     are real functionality enhancements and behaviour tweaks, so this
     approach really made no sense for them).
   - There is no security risk with this plugin loader extension, because
     it allows you to set which of the available plugins can be loaded
     on demand (all others must/can be included() as usual elsewhere).
   - This however requires administration of this plugins` configuration
     array, but that is not much more effort than maintaining a bunch of
     include() statements.
   - Is a small degree faster then including multiple plugin script files
     one by one. Alternatively you could also merge (cat, mkhuge) all
     wanted plugins into one script file so you get a speed improvement
     against multiple include() calls.
*/


$ewiki_plugins["dl"]["action"] = array(
	"view" => "",
	"info" => "",
#	"edit" => "spellcheck.php",
#	"calendar" => "calendar.php",
	"diff" => "diff.php",
	"like" => "like_pages.php",
);

$ewiki_plugins["dl"]["page"] = array(
	"PowerSearch" => "page_powersearch.php",
	"PageIndex" => "page_pageindex.php",
	"WordIndex" => "page_wordindex.php",
	"InterWikiMap" => "page_interwikimap.php",
	"OrphanedPages" => "page_orphanedpages.php",
	"WantedPages" => "page_wantedpages.php",
#	"SinceUpdatedPages" => "page_since_updates.php",
#	"FileDownload" => "downloads.php",
#	"FileUpload" => "downloads.php",
#	"AboutPlugins" => "page_aboutplugins.php",
	"RandomPage" => "page_randompage.php",
#	"Calendar" => "calendar.php",
#	"YearCalendar" => "calendar.php",
	"ImageGallery" => "page_imagegallery.php",
#	"Fortune" => "page_fortune.php",
#	"PhpInfo" => "page_phpinfo.php",
#	"ScanDisk" => "page_scandisk.php",
#	"WikiUserLogin" => "page_wikiuserlogin.php",
);


$ewiki_plugins["view_init"][] = "ewiki_dynamic_plugin_loader";


function ewiki_dynamic_plugin_loader(&$id, &$data, &$action) {

   global $ewiki_plugins, $ewiki_id, $ewiki_title, $ewiki_t,
          $ewiki_ring, $ewiki_author;

   if (empty($ewiki_plugins["page"][$id]) && ($file=$ewiki_plugins["dl"]["page"][$id])) {
      include(dirname(__FILE__)."/".$file);
   }
   elseif (empty($ewiki_plugins["action"][$action]) && ($file=$ewiki_plugins["dl"]["action"][$action])) {
      include(dirname(__FILE__)."/".$file);
   }

   return("");
}


?>