<?php

 /*
     This include script just opens the database connection;
     it is however an __examplary__ "configuration file" for
     ewiki.php (real config constants can be found in there!)
     If you have read and understood the README file, you will
     probably want to remove this example file!
 */


 #-- OPEN DATABASE for ewiki
 #
 function_exists("mysql_connect") &&
 mysql_connect("localhost", "root", "$password") &&
 mysql_query("USE test")
 ||
 include("plugins/db_flat_files.php");
   #
   # This is a convenience workaround for quick initial "setup" success for
   # some users that haven't touched the README file at all. Most others
   # will want to decide between MySQL and flat_files and not to activate
   # the latter just when something went wrong with the database connection!



 #-- predefine some of the configuration constants
 define("EWIKI_LIST_LIMIT", 25);
 define("EWIKI_HTML_CHARS", 1);


 #-- fix broken PHP setup
 if (!function_exists("get_magic_quotes_gpc") || get_magic_quotes_gpc()) {
    include("fragments/strip_wonderful_slashes.php");
 }
 if (ini_get("register_globals")) {
    include("fragments/strike_register_globals.php");
 }


 #-- plugins
 include("plugins/patchsaving.php");
 include("plugins/page_README.php");
 include("plugins/email_protect.php");
# include("plugins/pluginloader.php");
 include("plugins/page_powersearch.php");
 include("plugins/page_pageindex.php");
# include("plugins/page_wordindex.php");
# include("plugins/page_aboutplugins.php");
# include("plugins/page_imagegallery.php");
# include("plugins/page_orphanedpages.php");
# include("plugins/fancy_list_dict.php");
# include("plugins/diff.php");
# include("plugins/like_pages.php");
# include("plugins/notify.php");
 include("plugins/imgresize_gd.php");
# include("plugins/imgresize_magick.php");
# include("plugins/calendar.php");
# include("plugins/downloads.php");
# include("plugins/aview_downloads.php");
 include("plugins/markup_css.php");
# include("plugins/markup_paragraphs.php");
# include("plugins/markup_footnotes.php");
# include("plugins/markup_rescuehtml.php");
# include("plugins/rendering_pre.php");
# include("plugins/more_interwiki.php");
# include("plugins/link_css.php");
# include("plugins/link_icons.php");
# include("plugins/link_target_blank.php");
# include("plugins/mpi.php");
# include("plugins/aview_linktree.php");
# include("plugins/aview_backlinks.php");
# include("plugins/auth_perm_old.php");
# include("plugins/auth_perm_ring.php");
# include("plugins/auth_user_array.php");
# include("plugins/auth_method_http.php");
# include("plugins/binary_store.php");





 #-- library
 include("ewiki.php");


?>