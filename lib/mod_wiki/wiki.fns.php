<?php

// =========================================================================
// wiki.fns.php
// written by Jim Applegate
// uses the ewiki application located in the "classes" folder
//
// =========================================================================


// ------------------------------------------------------------------------
// TAG: wiki
//
// ------------------------------------------------------------------------

function wiki($wiki_name, $wiki_home="default")
{
   global $_HCG_GLOBAL;
   
   ini_set('magic_quotes_gpc', 1);

   if ($wiki_home == "default") {
      $wiki_home = $wiki_name . "Home";
   }
   
   // extract the passed variables from the global variable instead of
   // having them passed as parameters.
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      extract($_HCG_GLOBAL['passed_vars'], EXTR_OVERWRITE);
   }

   // TODO: set up to use ADODB
   mysql_connect($_HCG_GLOBAL['db']['hcg_public']['host'], $_HCG_GLOBAL['db']['hcg_public']['user'], $_HCG_GLOBAL['db']['hcg_public']['pass']);
   mysql_query("use ewiki");


   // plugins
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/patchsaving.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/README.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/email_protect.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/pluginloader.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/powersearch.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/pageindex.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page_wordindex.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page_aboutplugins.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page_imagegallery.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page_orphanedpages.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/fancy_list_dict.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/diff.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/like_pages.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/notify.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/feature/imgresize_gd.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/imgresize_magick.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/calendar.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/downloads.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview_downloads.php");
   include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup/css.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup_paragraphs.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup_footnotes.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup_rescuehtml.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/rendering_pre.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/rendering_phpwiki12.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/more_interwiki.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/link_css.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/link_icons.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/link_target_blank.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/mpi.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview_linktree.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview_backlinks.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth_perm_old.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth_perm_ring.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth_user_array.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth_method_http.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/binary_store.php");

   // these definitions were pulled from ewiki.php to localize them

   define("EWIKI_SCRIPT", $_SESSION['this_page']."?id=");  // global variable
   define("EWIKI_PAGE_INDEX", $wiki_home);
   define("EWIKI_SPLIT_TITLE", 1);            // <h2>Wiki Page Name</h2>
   define("EWIKI_PROTECTED_MODE", 0);         // disable funcs + require auth
   define("EWIKI_PROTECTED_MODE_HIDING", 0);  // hides disallowed actions
   define("EWIKI_AUTH_DEFAULT_RING", 3);      // 0=root 1=priv 2=user 3=view
   define("EWIKI_DB_TABLE_NAME", $wiki_name); // MySQL / ADOdb
   define("EWIKI_LOGLEVEL", 1);               // 0=error 1=warn 2=info 3=debug
   define("EWIKI_LOGFILE", $_HCG_GLOBAL['application_dir']."/logs/http/ewiki.log");

   //error_reporting(0);
   
   require_once("classes/ewiki-R1.01d4/ewiki.php");
   
   ini_set('magic_quotes_gpc', 0);

   return ewiki_page();

}

// ------------------------------------------------------------------------
// TAG: wiki2
//
// ------------------------------------------------------------------------

function wiki2($wiki_name, $wiki_home="default")
{
   global $_HCG_GLOBAL;
   
   ini_set('magic_quotes_gpc', 1);

   if ($wiki_home == "default") {
      $wiki_home = $wiki_name . "Home";
   }
   
   $this_page = (isset($_SESSION['this_page'])) ? $_SESSION['this_page'] : '';
   
   // extract the passed variables from the global variable instead of
   // having them passed as parameters.
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      extract($_HCG_GLOBAL['passed_vars'], EXTR_OVERWRITE);
   }

   // TODO: set up to use ADODB
   mysql_connect($_HCG_GLOBAL['db']['hcg_public']['host'], $_HCG_GLOBAL['db']['hcg_public']['user'], $_HCG_GLOBAL['db']['hcg_public']['pass']);
   mysql_query("use ewiki");
   
   // helper scripts for broken/outdated PHP configurations
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/lib/fix.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/lib/upgrade.php");

   // plugins
//   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/init.php");            # you can disable this later
//   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/page/README.php");     # this too
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/pluginloader.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/email_protect.php");
   require($_HCG_GLOBAL['ewiki2_dir']."/plugins/page/powersearch.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/page/pageindex.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/wordindex.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/aboutplugins.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/imagegallery.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/orphanedpages.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/spages.php") && ewiki_spages_init("tools/");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/filter/search_highlight.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/appearance/fancy_list_dict.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/patchsaving.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/action/diff.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/action/like_pages.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/jump.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/notify.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/feature/imgresize_gd.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/module/calendar.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/appearance/title_calendar.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/module/downloads.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview/downloads.php");
   include($_HCG_GLOBAL['ewiki2_dir']."/plugins/markup/css.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup/paragraphs.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup/footnotes.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/markup/rescuehtml.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/interwiki/intermap.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/linking/link_css.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/linking/link_icons.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/linking/link_target_blank.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/mpi/mpi.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview/linktree.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/aview/backlinks.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/filter/fun_wella.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/filter/fun_upsidedown.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/filter/fun_chef.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/page/textupload.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth/auth_perm_ring.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/userdb_registry.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/auth/auth_method_http.php");
   //include($_HCG_GLOBAL['ewiki_dir']."/plugins/db/binary_store.php");

   // these definitions were pulled from ewiki.php to localize them

   define("EWIKI_INIT_PAGES", "/var/opt/httpd/lib/classes/ewiki/");
   define("EWIKI_SCRIPT", "$this_page?id=");  // global variable
   define("EWIKI_PAGE_INDEX", $wiki_home);
   define("EWIKI_SPLIT_TITLE", 1);            // <h2>Wiki Page Name</h2>
   define("EWIKI_PROTECTED_MODE", 0);         // disable funcs + require auth
   define("EWIKI_PROTECTED_MODE_HIDING", 0);  // hides disallowed actions
   define("EWIKI_AUTH_DEFAULT_RING", 3);      // 0=root 1=priv 2=user 3=view
   define("EWIKI_DB_TABLE_NAME", $wiki_name); // MySQL / ADOdb
   define("EWIKI_LOGLEVEL", 1);               // 0=error 1=warn 2=info 3=debug
   define("EWIKI_LOGFILE", $_HCG_GLOBAL['application_dir']."/logs/http/ewiki.log");

   //error_reporting(0);
   
   require_once("classes/ewiki-R1.02b/ewiki.php");
   
   ini_set('magic_quotes_gpc', 0);

   return ewiki_page();

}

?>