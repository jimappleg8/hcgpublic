<?php

/*
   Load this with your web browser (http://localhost/ewiki/setup.php)
   to generate an "ewiki.ini" file by using a simple configuration
   wizard, which queries you about all the features (plugins) and
   settings you wish to use.

   Save the generated .ini file to disk and then load the "ini.php"
   script instead of "config.php" or "ewiki.php".
*/



#-- defaults for the separately handled database settings in $db[]
if (!($db = $_REQUEST["db"])) {
   $db = array(
     "type" => NULL,
     "server" => "localhost",
     "dbname" => "test",
     "table" => "ewiki",
     "dir" => "/tmp",
     "dba" => "/tmp/wiki.dbm",
   );
}


#-- read in ewiki.ini, if one was uploaded
if ($li = $_FILES["load_ini"]["tmp_name"]) {
   $ini = array();
   $uu = preg_split('/^\[(\w+)\]/m', implode("",file($li)), -1, PREG_SPLIT_DELIM_CAPTURE);
   for ($i=1; $i<=count($uu); $i+=2) {
      $sect = $uu[$i];
      preg_match_all('/^\s*(\w[^\s]+)\s*=[\t ]*(.+?)(\s;.+)?\s*$/m', $uu[$i+1], $rows);
      foreach ($rows[1] as $r=>$name) {
         $ini[$sect][$name][] = trim($rows[2][$r]);
      }
   }

   #-- pre-set the separate $db[] hash
   if ($ini["db"]) {
      foreach ($ini["db"] as $i=>$val) {
         $db[$i] = $val[0];
   }  }
}


#-- heavily mixed list of features and options
#
# - an array of arrays
# - each entry gives a 'feature' or simply a text fragment
# - first level subarrays have following entries
#   [0] type setting, "!"=headline, "="=always_enabled_feature,
#       0=disabled, 1=enabled, "..."=text_fragment_only
#   [1] title
#   [2] text / description
#   [3] list of plugin file names (without .php)
#   [4] another subarray of option settings
# - an option setting subarray has following structure:
#   [0] <input> field type
#   [1] title
#   [2] EWIKI_ constant or $ewiki_ var name
#   [3] default setting (value)
#   [4] text / description
#   [5] options for <select> input, separated by "|" with "values=titles"
# - html text fragments can be inserted anywhere in titles or text and
#   description entries (used for "database selection" part)
#
#
$list = array(

   #---------------------------------------------------------------------
   array(
      "!", "core settings"
   ),
   array(
      "=", "Page Names",
      "You can customize the names of the built-in page plugins.",
      array(),
      array(
         array("text", "", 'EWIKI_PAGE_INDEX', "ErfurtWiki", "the FrontPage, displayed as default"),
         array("text", "", 'EWIKI_PAGE_UPDATES', "UpdatedPages", "list of recently edited pages"),
         array("text", "", 'EWIKI_PAGE_NEWEST', "NewestPages", "newly created pages"),
         array("text", "", 'EWIKI_PAGE_SEARCH', "SearchPages", "page search function"),
         array("text", "", 'EWIKI_PAGE_POWERSEARCH', "PowerSearch", "enhanced search function"),
      ),
   ),
   array(
      "=", "<br>link generation:",
      "To generate correct hypertext references (&lt;a href=), ewiki needs to know the prefixes to use.",
      array(),
      array(
         array("text", "SCRIPT", 'EWIKI_SCRIPT', "?id=", "this will simply be used as prefix to page names; \"?page=\", \"?name=\" or simply \"?\" will work, but sometimes you need to give the script name too, like \"index.php?id=\""),
         array("text", "SCRIPT_URL", 'EWIKI_SCRIPT_URL', "", "the complete URL to your wiki script (including \"?id=\"), will be guessed if you don't set it"),
         array("text", "SCRIPT_BINARY", 'EWIKI_SCRIPT_BINARY', "", "(usually just \"?binary=\") will be often be guessed correctly, so you don't need to specify it"),
      ),
   ),
   array(
      "=", "<br>behaviour",
      "and general switches",
      array(),
      array(
         array("checkbox", "auto edit", 'EWIKI_AUTO_EDIT', "1", "will bring up a textarea for nonexistent pages"),
         array("checkbox", "edit redirect", 'EWIKI_EDIT_REDIRECT', "1", "initiates a http redirect after saving a page to work around typical page reload problems"),
         array("checkbox", "hit counting", 'EWIKI_HIT_COUNTING', "1", "count page views in database"),
         array("checkbox", "DNS resolving", 'EWIKI_RESOLVE_DNS', "1", "for author field when saving"),
         array("checkbox", "http headers", 'EWIKI_HTTP_HEADERS', "1", "(general switch to disable HTTP powers)"),
         array("checkbox", "binary database support", 'EWIKI_ENGAGE_BINARY', "1", "for image uploading"),
         array("checkbox", "image caching", 'EWIKI_CACHE_IMAGES', "1", "referenced images will be cached"),
         array("checkbox", "image maximum size", 'EWIKI_IMAGE_MAXSIZE', "65536", "up to that size (in bytes)"),
         array("text", "log", 'EWIKI_LOGFILE', "/tmp/ewiki.log", "to track a few important activities"),
         array("select", "log level", 'EWIKI_LOGLEVEL', "-1", "how much/important notes to log", "-1=nothing|0=errors|1=warnings|2=info/notes|3=debug"),
      ),
   ),


   #---------------------------------------------------------------------
   array(
      "!", "database",
      "ewiki has built-in MySQL support, but can also use various flat file backends or other common database types.<br>"
   ),
   array(
      "...", "",
      <<<EOT
  <br>
  <input type="radio" id="db-0" name="db[type]" value="none"><label for="db-0"> don't care</label>
   <span class="feature-desc">If you don't need to open the database connection in the config.php - if a MySQL connection was already established somewhere else</span>
   <br>
  <br>
  <input type="radio" id="db-1" name="db[type]" value="mysql"><label for="db-1"> built-in MySQL</label>   <br>
  <input type="radio" id="db-2" name="db[type]" value="pgsql"><label for="db-2"> or PostgreSQL</label> <span class="feature-desc">(with anydb_ wrapper)</span>
   <div class="option">
      <label for="db-1-1">server </label><input type="text" id="db-1-1" name="db[server]" value="$db[server]"><br>
      <label for="db-1-2">user name </label><input type="text" id="db-1-2" name="db[user]" value="$db[user]"><br>
      <label for="db-1-3">password </label><input type="password" id="db-1-3" name="db[pw]" value="$db[pw]"><br>
      <label for="db-1-4">database name </label><input type="text" id="db-1-4" name="db[dbname]" value="$db[dbname]"><br>
      <label for="db-1-5">table name </label><input type="text" id="db-1-5" name="db[table]" value="$db[table]"> will be created automatically, when you activate ewiki for the first time<br>
   </div>
  <br>
  <input type="radio" id="db-3" name="db[type]" value="flat"><label for="db-3"> flat file</label> <span class="feature-desc">database backend</span>   <br>
  <input type="radio" id="db-4" name="db[type]" value="fast"><label for="db-4"> fast file</label> <span class="feature-desc">(compressed)</span>   <br>
  <input type="radio" id="db-5" name="db[type]" value="dzf2"><label for="db-5"> new flat file backend 'dzf2'</label> <span class="feature-desc">(provides case-insensitive storage, plattform compatible, but more complicated structure)</span>
   <div class="option">
      <label for="db-3-1">storage directory </label><input type="text" id="db-3-1" name="db[dir]" value="$db[dir]">
      <span class="option-desc">Note: the directory "/tmp" exists on most
Unix/Linux webservers, but will be purged on reboot; so you normally want to
use a different location for your pages. Choose a <i>relative path name</i>
(like for example "<kbd>./files</kbd>") and create that directory ("<kbd>mkdir
<i>files</i></kbd>" in FTP/shell) and give it <i>world-write permissions</i>
("<kbd>chmod 777 <i>files</i></kbd>" in FTP/shell).</span>
   </div>
  <br>
  <input type="radio" id="db-6" name="db[type]" value="dba"><label for="db-6"> .dbm</label> <span class="feature-desc"> Berkely database file</span>  <br>
   <div class="option">
      <label for="db-6-1">database file </label><input type="text" id="db-6-1" name="db[dba]" value="$db[dba]">
      <span class="option-desc">The file name extension must be one of:
      .dbm, .db2, .db3, .db4, .ndbm, .gdbm or .flatfile, and the file must
      be world-writable of course</span>
   </div>
EOT
   ),


   #---------------------------------------------------------------------
   array(
      "!", "major plugins"
   ),
   array(
      1, "EMailAddressProtection",
      "You can safely enter email addresses in your Wiki, because they will be protected from spambots.",
      array("plugins/email_protect"), array()
   ),
   array(
      0, "Click-and-run .xpi plugins",
      "are the easiest way to add new features to your Wiki. Just see <a href=\"./?PlugInstall\">PlugInstall</a>, once it is running.",
      array("plugins/feature/xpi"),
      array(
         array("password", "admin password", '$ewiki_config["xpi_pw"][]', "", "You have to setup an administrator password for this feature. Please write it down."),
//       array("checkbox", "unrestricted .jpi", "XPI_EVERYBODY_JPI", "1", "Anyone else may install the safe .jpi plugins (which run in a sandboxed JavaScript interpreter)"),
      )
   ),
   array(
      1, "StaticPages",
      "allows you to put files (.html or .txt type, or .php scripts) into the spages/ directory, which will be served as uneditable pages.",
      array("plugins/spages"),
      array(),
   ),
   array(
      1, "markup plugins (mpi)",
      "can be run from individual pages and embed dynamic content, the various plugins are loaded on demand",
      array("plugins/mpi/mpi"),
      array(),
   ),

   #---------------------------------------------------------------------
   array(
      "!", "features"
   ),
   array(1, "jump", "provides the [jump:PageName]", array("plugins/jump"), array(), ),
   array(1, "notify", "allows for page change notifications", array("plugins/notify"), array(), ),
   array(0, "patchsaving", "works around concurrent edits (two users trying to edit and save the same page)", array("plugins/patchsaving"), array(), ),
   array(0, "plugin loader", "can automatically load a few registered plugins on demand / when needed", array("plugins/pluginloader"), array(), ),
   array(1, "image resizing PHP libgd", "scales images down, when uploaded on the edit page", array("plugins/feature/imgresize_gd"), array(), ),
   array(0, "image resizing ImageMagick", "requires an installed binary on the server", array("plugins/feature/imgresize_magick"), array(), ),
   array(0, "spellcheck", "enhances the edit preview function", array("plugins/feature/spellcheck"), array(), ),
   array(0, "imagefile naming", "beautyfies generated internal:// links", array("plugins/feature/imgfile_naming"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "page plugins",
      "provide dynamic functions/lists (usually with input forms):<br>",
   ),
   array(1, "PowerSearch", "a more featureful page search function", array("plugins/page/powersearch"), array(), ),
   array(1, "PageIndex", "lists all pages", array("plugins/page/pageindex"), array(), ),
   array(1, "WordIndex", "prints words used in page names", array("plugins/page/wordindex"), array(), ),
   array(1, "OrphanedPages", "lists unlinked pages", array("plugins/page/orphanedpages"), array(), ),
   array(1, "WantedPages", "lists absent pages, where however links to exist", array("plugins/page/wantedpages"), array(), ),
   array(0, "HitCounter", "displays sum of hits to all pages", array("plugins/page/hitcounter"), array(), ),
   array(0, "ImageGallery", "shows all uploaded pictures", array("plugins/page/imagegallery"), array(), ),
   array(0, "InterWikiMap", "shows list of all known InterMap: prefixes", array("plugins/page/interwikimap"), array(), ),
   array(0, "SinceUpdates", "...", array("plugins/page/since_updates"), array(), ),
   array(0, "AboutPlugins", "security risk", array("plugins/page/aboutplugins"), array(), ),
   array(0, "PhpInfo", "security risk", array("plugins/page/phpinfo"), array(), ),
   array(1, "TextUpload", "allows to upload pages in various formats", array("plugins/page/textupload"), array(), ),
   array(0, "WikiDump", "users may download all pages .zip", array("plugins/page/wikidump"), array(), ),
   array(0, "WikiNews", "summarizes newly created pages", array("plugins/page/wikinews"), array(), ),
   array(0, "RandomPage", "", array("plugins/page/randompage"), array(), ),
   array(0, "Fortune", "nonsense", array("plugins/page/fortune"), array(), ),
   array(0, "ScanDisk", "nonsense", array("plugins/page/scandisk"), array(), ),
   array(0, "WikiUserLogin", "allows to set an unverified username", array("plugins/page/wikiuserlogin"), array(), ),
   array(0, "AddNewPage", "newbies/office users like that", array("plugins/page/addnewpage"), array(), ),
   array(0, "CreateNewPage", "newbies/office users like that", array("plugins/page/createnewpage"), array(), ),
   array(0, "RecentChanges", "provides a fancier UpdatedPages list, available in two styles:", array("plugins/page/rexentchanges"),
      array(array("select", "layout", '$ewiki_plugins["rc"][0]', "ewiki_page_rc_usemod", "", "ewiki_page_rc_usemod=UseMod|ewiki_page_rc_moin=MoinMoin")),
   ),

#   array(0, "SearchAndReplace", "requires an enabled auth module", array("plugins/admin/page_searchandreplace"), array(), ),
#   array(0, "SearchCache", "caches page plugins output for access in the search functions, requires authentication", array("plugins/admin/page_searchcache"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "action plugins",
      "can be activated on single pages to perform certain actions with them:<br>",
   ),
   array(0, "like pages", "shows up a list of similar named pages", array("plugins/action/like_pages"), array(), ),
   array(0, "diff", "shows differences between two page versions", array("plugins/action/diff"), array(), ),
   array(0, "GNU diff", "enhanced version (uses sytem utility)", array("plugins/action/diff_gnu"), array(), ),
   array(0, "raw", "allows to download a pages 'source code'", array("plugins/action/raw"), array(), ),
   array(0, "automatic translation", "provides a BabelFish or GoogleFish link", array("plugins/action/translation"), array(), ),
#   array(0, "control", "can be used to rename or delete individual pages, requires working authentication system", array("plugins/admin/control"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "modules",
      "larger extension plugins:<br>", 
   ),
   array(0, "Calendar", "can be attached to every page", array("plugins/module/calendar"), array(), ),
   array(0, "downloads", "and uploads of files into sections", array("plugins/module/downloads"), array(), ),
   array(0, "tour", "through pages (with previews)", array("plugins/module/tour"), array(), ),


   #---------------------------------------------------------------------
   array(
      "!", "markup extensions",
   ),
   array(1, "general CSS support", "allows to inject style commands into pages (@@ syntax)", array("plugins/markup/css"), array(), ),
   array(0, "footnotes", "shouldn't be necessary in a Wiki", array("plugins/markup/footnotes"), array(), ),
   array(0, "basic html tags", "could be used in pages", array("plugins/markup/rescuehtml"), array(), ),
   array(0, "Smilies", "are automatically replaced with images; you must install separately", array("plugins/markup/smilies"),
      array(
         array("text", "image dir", "SMILIES_DIR", "./img/smilies/", "where you have the pictures"),
         array("text", "base href", "SMILIES_BASE_HREF", "/img/smilies/", "is used as &lt;img src= prefix"),
      ),
   ),
   array(0, "one char emphasis", "allows to use *bold* and /italic/", array("plugins/markup/1emphasis"), array(), ),
   array(0, "natural lists", "can start with '1.' or '7)' instead of #", array("plugins/markup/naturallists"), array(), ),
   array(0, "fixed/faster source mangling", "occasionally enhances a few functions", array("plugins/markup/fix_source_mangling"), array(), ),
#   array(0, "update_format", "", array("plugins/markup/update_format"), array(), ),
   array(0, "abbreviations", "are hosted in table or definition lists on dedicated pages, and then become tooltips everywhere else",
      array("plugins/markup/abbr"),
      array(
         array("text", "acronyms page", '$ewiki_config["acronym"][0]', "Acronyms", ""),
         array("text", "abbreviations", '$ewiki_config["abbr"][0]', "Abbreviations", ""),
      ),
   ),
   array(0, "html syntax tables", "are useful for larger tables", array("plugins/markup/htmltable"), array(), ),
   array(0, "ASCII art tables", "are rarely used", array("plugins/markup/asciitbl"), array(), ),
   array(0, "table rowspan", "to join table cells", array("plugins/markup/table_rowspan"), array(), ),
   array(
      "...", "", "<br>ewiki can partially emulate:<br>"
   ),
   array(0, "UseMod", "", array("plugins/markup/usemod"), array(), ),
   array(0, "PhpWiki", "", array("plugins/markup/phpwiki"), array(), ),
   array(0, "SfWiki", "", array("plugins/markup/sfwiki"), array(), ),
#   array(0, "Miki", "", array("plugins/markup/miki"), array(), ),
   array(0, "bbcode", "", array("plugins/markup/bbcode"), array(), ),

   array(
      "...", "", "<br>filters:<br>"
   ),
   array(0, "fiXhtml filter", "tries to work around various formatting errors, and can even try to convince ewiki to generate valid XHTML", array("plugins/filter/f_fixhtml"),
      array(),
   ),
   array(0, "MSIE .png support", "instructs IE to use an ActiveX imageloader for .png images", array("plugins/filter/f_msiepng"), array(), ),
   array(0, "scream-o-matic", "uppercases all pages contents, if a user enters too much uppercase on a page", array("plugins/filter/fun_screamomatic"), array(), ),


   #---------------------------------------------------------------------
   array(
      "!", "linking",
   ),
   array(
      "=", "case insensitive wiki links", "",
      array(),
      array(
         array("checkbox", "enable", "EWIKI_CASE_INSENSITIVE", "1", "don't work with flat file databases on Unix file systems, you have to use <tt>dzf2</tt> then"),
      ),
   ),
   array(1, "UseMod compatible <a href=\"./?LinkDataBase\">LinkDataBase</a>", "is loved by all web spiders", array("plugins/linking/linkdatabase"), array(), ),
   array(0, "autolinking / GAGA parser", "links single words, if the according pages exist; but needs some administration", array("plugins/linking/autolinking"), array(), ),
   array(1, "link_css", "adds CSS information for link types", array("plugins/linking/link_css"), array(), ),
   array(0, "plural", "initiates a fuzzy page name matching (slow)", array("plugins/linking/plural"), array(), ),
   array(0, "language negotiation", "selects the correct page if multiple variants exist (PageName.en, PageName.fr)", array("plugins/linking/tcn"), array(), ),
   array(0, "excerpt tooltips", "for linked pages", array("plugins/linking/linkexcerpts"), array(), ),
   array(0, "title swapping", "allows to use wrong [page|title] syntax", array("plugins/linking/titlefix"), array(), ),
   array(0, "instanturls", "allows naming URLs in a table or definition list on the page <a href=\"./?InstantURLs\">InstantURLs</a> or", array("plugins/linking/instanturls"),
      array(array("text", "url abbreviations page", '$ewiki_config["instant_url_pages"][]', "")),
   ),
   array(0, "instanturl_find", "introduces the [find:..] moniker, which searches for URL abbreviations/names in the interwiki and instanturls pages, falls back to fuzzy page search or Google", array("plugins/linking/instanturl_find"), array(), ),
   array(0, "selflink to MetaWiki", "", array("plugins/linking/selfmetawiki"), array(), ),
   array(0, "selfbacklink", "", array("plugins/linking/selfbacklinks"), array(), ),
   array(0, "selfsearch", "", array("plugins/linking/selfsearch"), array(), ),
#   array(0, "link_icons", "", array("plugins/linking/link_icons"), array(), ),
   array(0, "target=_blank", "for all external links", array("plugins/linking/link_target_blank"), array(), ),
   array(
      "...", "", "<br>InterWiki functionality:<br>"
   ),
   array(0, "InterMap", "enlarges the list of knwon InterWiki: monikers", array("plugins/interwiki/intermap"), array(), ),
   array(0, "PublicallyEditableIntermap", "eases extension of the prefix list", array("plugins/interwiki/editable"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "appearance tweaks",
      "",
   ),
   array(
      "=", "general settings", "",
      array(),
      array(
         array("checkbox", "print page titles", "EWIKI_PRINT_TITLE", 1, "page name on top of every page"),
         array("checkbox", "split link titles", "EWIKI_SPLIT_TITLE", 0, "separates WikiWords into parts"),
         array("checkbox", "show action links", "EWIKI_CONTROL_LINE", 1, "EditThisPage, PageInfo, and so on"),
         array("text", "page lists", "EWIKI_LIST_LIMIT", "20", "number of pages shown at once"),
         array("checkbox", "edit thank you", '$ewiki_config["edit_thank_you"]', 1, "displayed after page edited and saved"),
         array("text", "edit box size", '$ewiki_config["edit_box_size"]', "70x15", "columns by rows"),
      ),
   ),
   array(0, "fancy dictionary like listing", "for PageIndex and WordIndex", array("plugins/appearance/fancy_list_dict"), array(), ),
   array(0, "page lists &lt;br&gt;", "", array("plugins/appearance/listpages_br"), array(), ),
   array(0, "page lists &lt;table&gt;", "", array("plugins/appearance/listpages_tbl"), array(), ),
   array(0, "page lists &lt;ul&gt;", "", array("plugins/appearance/listpages_ul"), array(), ),
   array(0, "calendar page titles", "can be made more readable", array("plugins/appearance/title_calendar"), array(), ),
   array(0, "search term highlighting", "encolours the words from a previous search that led to the current page (links from PowerSearch or Google)", array("plugins/filter/search_highlight"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "view append",
      "things that can be displayed below each page (under action links):<br>"
   ),
   array(0, "BackLinks", "is the list of pages linking to the current", array("plugins/aview/backlinks"), array(), ),
   array(0, "LinkTree", "shows the paths to the root page (slow)", array("plugins/aview/linktree"), array(), ),
   array(0, "downloads", "expands file uploading to individual pages",
      array("plugins/aview/downloads", "plugins/module/downloads"),
   ),
   array(0, "image appending", "with a small form", array("plugins/aview/imgappend"), array(), ),
   array(0, "SubPages", "of the current one have similar names", array("plugins/aview/subpages"), array(), ),
   array(0, "table of contents", "is generated from pages headlines", array("plugins/aview/toc"), array(), ),
#   array(0, "fpage_logo", "", array("plugins/aview/fpage_logo"), array(), ),
#   array(0, "fpage_copyright", "", array("plugins/aview/fpage_copyright"), array(), ),
#   array(0, "control2", "", array("plugins/aview/control2"), array(), ),
#   array(0, "piclogocntrl", "", array("plugins/aview/piclogocntrl"), array(), ),
#   array(0, "posts", "", array("plugins/aview/posts"), array(), ),
#   array(0, "threads", "", array("plugins/aview/threads"), array(), ),
   array(
      "...", "", "<br>things added when editing a page:<br>"
   ),
   array(0, "templates", "presents list of ...Template pages for yet blank pages", array("plugins/aview/aedit_templates"), array(), ),
   array(0, "changelog", "allows a change summary, later reused on RecentChanges", array("plugins/aview/aedit_log"), array(), ),
#   array(0, "aedit_pageimage", "", array("plugins/aview/aedit_pageimage"), array(), ),
   array(0, "free AuthorName setting", "works much like WikiUserLogin, but presents it author name choosing field on the edit/ page", array("plugins/aview/aedit_authorname"), array(), ),
#   array(0, "aedit_deletebutton.js", "", array("plugins/aview/aedit_deletebutton.js"), array(), ),


   #---------------------------------------------------------------------
   array(
      "!", "meta data extensions",
      "things that can be displayed below each page (under action links):<br>"
   ),
   array(0, "general meta data support", "adds a small meta data input box below the page edit/ box", array("plugins/meta/meta"), array(), ),
   array(0, "page trails", "&lt;&lt; Prev | ParentPage | Next &gt;&gt;", array("plugins/aview/pagetrail"), array(), ),
   array(0, "page title setting", "allows to override the displayed page title: with meta info", array("plugins/meta/f_title"), array(), ),
   array(0, "builtin categories", "must be tweaked first", array("plugins/meta/builtincategories"), array(), ),


   #---------------------------------------------------------------------
   array(
      "!", "asorted additions",
      ""
   ),
   array(1, "magic_slashes:=off", "works around magic_slashes_gpc (on outdated PHP installations)", array("plugins/input_trimming"), array(), ),
   array(1, "register_globals:=off", "works around (potentially harmful) enabled register_globals", array("fragments/strike_register_globals"), array(), ),
   array(0, "input securing", "tries to cut input data before processing starts", array("plugins/input_trimming"), array(), ),
   array(0, "page caching", "stores a copy of fully rendered pages for speed enhancement", array("plugins/lib/cache"), array(), ),
   array(0, "HTTP conditional requests", "cooperates with caches/proxies", array("plugins/lib/speed"), array(), ),
   array(0, "mime magic data", "adds a built-in auto detection for mime types, helpful to the downloads extension", array("plugins/lib/mime_magic"), array(), ),
#   array(0, "wikiapi", "", array("plugins/lib/wikiapi"), array(), ),
#   array(0, "js", "", array("plugins/lib/js"), array(), ),
#   array(0, "navbar", "", array("plugins/lib/navbar"), array(), ),

   #---------------------------------------------------------------------
   array(
      "!", "things they left out",
      "ewiki comes with a lot more extensions than listed here, but to
not overcomplicate this wizard we don't bother you with all possible
settings.<br>
But then this is also to encourage you to read the provided documentation.
<br><br>
The missing parts are:<br>
� authentication extensions<br>
� plugins that need more extensive customization<br>
� exotic database backends<br>
� funny extensions<br>
� recently added plugins<br>
",
   ),

/*
#   array(0, "admintrigger", "", array("plugins/admin/admintrigger"), array(), ),
   array(0, "binary_store", "", array("plugins/db/binary_store"), array(), ),
   array(0, "phpwiki13", "", array("plugins/db/phpwiki13"), array(), ),
#   array(0, "warn_utf8", "", array("plugins/debug/warn_utf8"), array(), ),
#   array(0, "subwiki", "", array("plugins/feature/subwiki"), array(), ),
   array(0, "spellcheck2", "", array("plugins/feature/spellcheck2"), array(), ),
#   array(0, "appendcomments", "", array("plugins/feature/appendcomments"), array(), ),
#   array(0, "appendonly", "", array("plugins/feature/appendonly"), array(), ),
#   array(0, "appendwrite", "", array("plugins/feature/appendwrite"), array(), ),
*/

);






#---------------------------------------------------------------------------




#-- inject values (into $list[]) imported from earlier loaded ewiki.ini
if ($ini) {
   foreach ($list as $fid=>$row) {

      #-- enable feature, if all requ/mentioned plugins were loaded in .ini
      if (($row[0]===0) || ($row[0]===1)) { 
         $is = all_in_array($row[3], $ini["plugins"]["load"]);
         $list[$fid][0] = ($is ? 1 : 0);
      }

      #-- set feature options
      if ($row[4]) {
         foreach ($row[4] as $oid=>$opts) {
            $name = $opts[2];
            $val = $ini["settings"][$name][0];
            if (strlen($val)) {
               $list[$fid][4][$oid][3] = $val;
      }  }  }
}  }


#-- compare two arrays, all elements of first must be in second
function all_in_array($a1, $a2) {
   $a3 = array_intersect($a1, $a2);
   return(count($a1) == count($a3));
}


#---------------------------------------------------------------------------



#-- prepare generation of config.php or ewiki.ini
#   (builds plugin and constant/var lists from _REQUEST settings)
#
if ($_REQUEST["feature"]) {

  $set = &$_REQUEST["feature"];
  $opt = &$_REQUEST["option"];

  $c_plugins = array();
  $c_settings = array(0=>array(), 1=>array());

  #-- go through hardcoded feature $list
  foreach ($list as $fid=>$row) {

     #-- compare if feature array enabled in _REQUEST
     $enabled = ($row[0] === "=") || ($set[$fid]);
     if ($enabled) {
#echo "ENABLED=$set[$fid] feature[$fid], r0=$row[0], r1=$row[1],\n";

        #-- list of plugins (always triggered)
        if ($plugins = $row[3]) {
           foreach ($plugins as $v) {
              $c_plugins[] = $v;
           }
        }

        #-- settings, individual $_REQUEST entries
        if ($options = $row[4]) {
           foreach ($options as $oid=>$row) {
              $i = $row[2];
              $v = $opt[$fid][$oid];
              if (strlen($v)) {
                 $var = ($i[0] == "$") ? 1 : 0;
                 $c_settings[$var][$i] = preg_match('/^\d+$/', $v) ? "$v" : "'$v'";
              }
           }
        }

     }
  }#--if($enabled)

}#--if(<submit>)


#---------------------------------------------------------------------------



#-- write out "config.php" file
#
if ($_REQUEST["config_php"]) {

#header("Content-Type: text/plain");
  header("Content-Type: application/x-httpd-php");
  header("Content-Disposition: attachment; filename=\"config.php\"");

  #-- write out config.php
  echo <<<EOT
<?php
# automatically generated config.php
# (see the ewiki configuration wizard)
#\n\n
EOT;

  echo "#-- database connection/plugins\n";
  switch ($db["type"]) {
     case "mysql":
        echo "// MySQL support is built-in, we only open the connection\n";
        echo "define(\"EWIKI_DB_TABLE_NAME\", \"$db[table]\");\n";
        echo "mysql_connect('$db[server]', '$db[user]', '$db[pw]');\n";
        echo "mysql_query('USE $db[dbname]');\n\n";
        break;

     case "pgsql":
        echo "define(\"EWIKI_DB_TABLE_NAME\", \"$db[table]\");\n";
        echo "define(\"EWIKI_DB_UTF8\", 0);  //depends on your Postgres db\n";
        echo "include(\"plugins/db/any.php\");\n";
        echo "\$db = anydb_connect('', '$db[user]', '$db[pw]', '$db[dbname]', 'pgsql');\n\n";
        break;

     case "fast":
        echo "define(\"EWIKI_DB_FAST_FILES\", 1);\n";
     case "flat":
        echo "define(\"EWIKI_DBFILES_DIRECTORY\", \"$db[dir]\");\n";
        echo "include(\"plugins/db/flat_files.php\");\n";
        echo "// the given directory must exist and be world-writable (chmod 777)\n\n";
        break;        

     case "dzf2":
        echo "define(\"EWIKI_DBFILES_DIRECTORY\", \"$db[dir]\");\n";
        echo "define(\"EWIKI_DBFF_ACCURATE\", 1);\n";
        echo "define(\"DZF2_HIT_COUNTING, 1);\n";
        echo "include(\"plugins/db/dzf2.php\");\n";
        echo "// the given directory must exist and be world-writable (chmod 777)\n\n";
        break;

     case "dba":
        echo "define(\"EWIKI_DBA\", \"$db[dba]\");\n";
        echo "include(\"plugins/db/dba.php\");\n";
        break;

     default:
        echo "// you must open a connection (MySQL) outside of the config.php,\n";
        echo "// it has not been configured with the setup wizard\n\n";
        break;
  }

  echo "#-- constants\n";
  foreach ($c_settings[0] as $id=>$val) {
     echo "define(\"$id\", $val);\n";
  }

  echo "\n#-- load plugins\n";
  foreach ($c_plugins as $file) {
     echo "include(\"{$file}.php\");\n";
  }

  echo "\n#-- set a few configuration variables\n";
  foreach ($c_settings[1] as $id=>$val) {
     echo "$id = $val;\n";
  }

  echo "\n#-- load ewiki 'lib'\ninclude(\"ewiki.php\");\n\n";
  echo "?" . ">";
  die();
}


#---------------------------------------------------------------------------



#-- write out as "ewiki.ini" file
#
if ($_REQUEST["ewiki_ini"]) {

#header("Content-Type: text/plain");
  header("Content-Type: text/x-ini-file");
  header("Content-Disposition: attachment; filename=\"ewiki.ini\"");

  #-- write out config.php
  echo "; automatically generated configuration summary\n; see ewiki config wizard\n";
  echo "\n[db]\n";
  foreach ($db as $id=>$val) {
     echo "$id = $val\n";
  }
  echo "\n[settings]\n";
  $c_settings = array_merge($c_settings[0], $c_settings[1]);
  foreach ($c_settings as $id=>$val) {
     $val = trim($val, "'");
     echo "$id = $val\n";
  }
  echo "\n[plugins]\n";
  echo "dir = plugins/\n";
  echo "ext = .php\n";
  foreach ($c_plugins as $file) {
     echo "load = $file\n";
  }
  echo "\n\n";
  die();
}



#---------------------------------------------------------------------------
# <html> page output otherwise

?>
<html>
<head>
 <title>ewiki configuration wizard</title>
<style type="text/css"><!--
html {
  show-tags: as-you-like-dear-browser;
}
body {
  margin: 0px; padding: 0px;
  font: Verdana,sans-serif 16px;
  color: #dddddd;
}
.left-bar {
  margin: 0px;
  float: left;
  width: 140px;
  height: 6000px;
  padding: 0px 0px 0px 20px;
}
.left-bar .stripe {
  width: 80px;
  height: 20%;
}
.real-body {
  padding-left: 40px;
  width: 520px;
}
h1,h2,h3,h4,h5 {
  background-color: #4c4c4e;
  color: #ffffff;
  margin-bottom: 3pt;
}
h2 {
  background-color: #464646;
  font-size: 20px;  
}
h1 {
  background-color: #404040;
  font-size: 24px;
}
input,checkbox,textarea,select {
  background-color: #666666;
  border: 1px solid #444444;
  color: #dddddd;
}
input:focus {
  border-color: #663333;
}
.feature-desc, .option-desc {
  color: #aaaaaa;
  font-size: 80%;
}
tt {
  font-size: 120%;
}
.option {
  color: #bbbbbb;
}
.option-desc {
  font-size: 75%;
}
a {
  color: #dddddd;
  text-decoration: none;
  border-bottom: dashed 1px #773333;
}
//--></style>
</head>
<body bgcolor="#555555"><div class="left-bar">
  <div class="stripe" style="background:#662222">&nbsp;</div>
  <div class="stripe" style="background:#642424">&nbsp;</div>
  <div class="stripe" style="background:#622626">&nbsp;</div>
  <div class="stripe" style="background:#602828">&nbsp;</div>
  <div class="stripe" style="background:#5E2A2A">&nbsp;</div>
</div>
<br>
<div class="real-body">
  <h1>ewiki configuration wizard</h1>
  This little utility allows you to generate an initial <tt>ewiki.ini</tt>
  or <tt>config.php</tt> by simply selecting the features and options you
  want to have in your Wiki.
  <br><br>
  
  Because there are so many plugins and extensions for ewiki, this
  list here isn't any shorter (though some things are left out);
  and you shouldn't therefore bother with everything in too deep
  detail initially. Just change the settings that sound most important
  or interesting to you.
  
  <br><br>

  <!-- self referring form -->
  <form action="setup.php" method="POST" enctype="multipart/form-data" accept-encoding="iso-8859-1">

  <h2>load a previous ewiki.ini</h2>
  You can reuse an earlier <tt>ewiki.ini</tt>, if you kept
  a copy of your previously choosen settings:
  <br>
  <input size="32" type="file" name="load_ini"> <input type="submit" value="load it">
  <br><br>
  
<?php

  #-- go through list
  foreach ($list as $fid=>$row) {

     #-- print main feature field
     switch ($row[0]) {

        case "0":
        case "1":
           echo "  <input type=\"checkbox\" id=\"feature-$fid\" name=\"feature[$fid]\" value=\"1\" ".($row[0]?"checked":"").">\n";
           echo "  <label for=\"feature-$fid\">$row[1]</label>\n";
           if ($row[2]) {
              echo "  <span class=\"feature-desc\">$row[2]</span>";
           }
           echo "<br>\n";
           break;

        case "=":
           echo "  <input type=\"hidden\" name=\"feature[$fid]\" value=\"1\">\n";
           echo "  $row[1]\n";
           if ($row[2]) {
              echo "  <span class=\"feature-desc\">$row[2]</span>";
           }
           echo "<br>\n";
           break;

        case "!":
           echo "  <h3>$row[1]</h3>\n";
        default:
           echo "  $row[2]\n";
           break;
     }

     #-- show up associated options
     if ($options = $row[4])
     foreach ($options as $oid=>$row) {
        $id = "option-$fid-$oid";
        echo '    <div class="option">';
        echo " &nbsp; &nbsp; <label for=\"$id\">$row[1]</label> ";
        switch($row[0]) {
           case "checkbox":
              $checked = ($row[3] ? " checked" : "");
              echo "<input type=\"checkbox\" name=\"option[$fid][$oid]\" id=\"$id\" value=\"1\"$checked>";
              break;
           case "select":
              echo "<select name=\"option[$fid][$oid]\" id=\"$id\">";
              foreach (explode("|", $row[5]) as $val) {
                 $title = $val;
                 if (strpos($val, "=")) {
                    list($val, $title) = explode("=", $val, 2);
                 }
                 $checked = (($row[3]==$value) ? " selected" : "");
                 echo "<option value=\"$val\"$selected>$title</option>";
              }
              echo "</select>";
              break;
           default:
              echo "<input type=\"$row[0]\" name=\"option[$fid][$oid]\" id=\"$id\" value=\"$row[3]\">";
        }
        echo "<span class=\"option-desc\"> $row[4]</span></div>\n";
     }
  }

?>
  <br>
  <h2>fin</h2>
  Now, that you've finished clicking around, you can save your configuration
  settings. A save dialog will open, and you should store the files directly
  into your ewiki/ directory.
  <br><br>
  <input style="color:#ffffff" type="submit" name="config_php" value="save config.php"> is
  what you should do now; you can use it as replacement for the example
  file distributed with ewiki.
  <br><br>
  <input type="submit" name="ewiki_ini" value="save ewiki.ini"> is
  useful to later come back and reuse the settings you've made here.
  <br><br>    

  </form>
</div>
</body>
</html>
