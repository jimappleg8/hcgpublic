<?php  chdir(dirname(__FILE__));

/*
  This EXAMPLARY config script just opens the database connection
  and loads a few extensions plugins and the core lib (ewiki.php).
  There is no need to keep it, or to stuck with the format used
  herein.
  You could as well put all the include() and define() statements
  into your own Wiki wrapper script (index.php) instead.
*/


#-- open mysql database connection if available,
#   or load the flat file database backend as fallback
#
if (function_exists("mysql_connect")) {
   $ok = @mysql_connect("localhost", "root", $password="")
         && mysql_query("USE test");
}
if (!$ok) {
// define("EWIKI_DBFILES_DIRECTORY", "/tmp");
   include_once("plugins/db/flat_files.php");
}


#-- only loaded if it exists
@include_once("local/config.php");

#-- predefine some of the configuration constants
define("EWIKI_LIST_LIMIT", 25);
define("EWIKI_HTML_CHARS", 1);
@define("EWIKI_PRINT_TITLE", 1);
 // define("EWIKI_SCRIPT", "?id=");
 // define("EWIKI_SCRIPT_URL", "http://www.example.com/wiki/index.php/");
 // ...
 // setlocale(LC_TIME, "nl");
   #
   # Note: constants in PHP can be defined() just once, so defining them
   # here makes sense, the settings won't get overridden by the defaults
   # in "ewiki.php" - you should likewise copy other settings from there
   # to here, if you wish to change some of them


#-- helper scripts for broken/outdated PHP configurations
include_once("plugins/lib/fix.php");
include_once("plugins/lib/upgrade.php");


#-- load plugins, before core script
 include_once("plugins/init.php");            # you can disable this later
 include_once("plugins/page/README.php");     # this too
# include_once("plugins/pluginloader.php");
 include_once("plugins/email_protect.php");
 include_once("plugins/page/powersearch.php");
 include_once("plugins/page/pageindex.php");
# include_once("plugins/page/wordindex.php");
# include_once("plugins/page/aboutplugins.php");
# include_once("plugins/page/imagegallery.php");
# include_once("plugins/page/orphanedpages.php");
# include_once("plugins/spages.php") && ewiki_spages_init("tools/");
# include_once("plugins/filter/search_highlight.php");
# include_once("plugins/appearance/fancy_list_dict.php");
# include_once("plugins/patchsaving.php");
# include_once("plugins/action/diff.php");
# include_once("plugins/action/like_pages.php");
# include_once("plugins/jump.php");
 include_once("plugins/notify.php");
 include_once("plugins/feature/imgresize_gd.php");
# include_once("plugins/feature/imgresize_magick.php");
# include_once("plugins/module/calendar.php");
# include_once("plugins/appearance/title_calendar.php");
# include_once("plugins/module/downloads.php");
# include_once("plugins/aview/downloads.php");
 include_once("plugins/markup/css.php");
# include_once("plugins/markup/paragraphs.php");
# include_once("plugins/markup/footnotes.php");
# include_once("plugins/markup/rescuehtml.php");
# include_once("plugins/interwiki/intermap.php");
# include_once("plugins/linking/link_css.php");
# include_once("plugins/linking/link_icons.php");
# include_once("plugins/linking/link_target_blank.php");
# include_once("plugins/mpi/mpi.php");
# include_once("plugins/aview/linktree.php");
# include_once("plugins/aview/backlinks.php");
# include_once("plugins/filter/fun_wella.php");
# include_once("plugins/filter/fun_upsidedown.php");
# include_once("plugins/filter/fun_chef.php");
# include_once("plugins/page/textupload.php");
# include_once("plugins/auth/auth_perm_ring.php");
# include_once("plugins/userdb_registry.php");
# include_once("plugins/auth/auth_method_http.php");
# include_once("plugins/db/binary_store.php");
# ...


#-- and finally load the core library
include_once("ewiki.php");


?>