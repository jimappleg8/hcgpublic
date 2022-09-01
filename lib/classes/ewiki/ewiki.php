<?php @define("EWIKI_VERSION", "R1.01a2");

/*

  ErfurtWiki - a very extensible, fast and user-friendly wiki engine
  ¯¯¯¯¯¯¯¯¯¯
  This is Public Domain (no license, no warranty); but feel free
  to redistribute it under GPL or anything else you like.
  (c) 2004 WhoEver wants to.

  project+help:
  http://erfurtwiki.sourceforge.net/
    http://ewiki.berlios.de/
  maintenance:
    Mario Salzer <mario*erphesfurt·de>
    Andy Fundinger <andy*burgiss·com>

  use it from inside yoursite.php like that:
    <html><body>...
    <?php
       include("ewiki.php");
       echo ewiki_page();
    ?>

*/
#-- you could also establish a mysql connection in here, of course:
// mysql_connect(":/var/run/mysqld/mysqld.sock", "user", "pw")
// and mysql_query("USE mydatabase");

        #-------------------------------------------------------- config ---
	// ADDED JBA
	global $ewiki_t;
	global $ewiki_plugins;
	define("EWIKI_BASE_DIR", "/var/opt/httpd/lib/classes/ewiki/");
	// end JBA

        #-- I'm sorry for that, but all the @ annoy me
        error_reporting(0x0000377 & error_reporting());

	#-- the absolute position of your ewiki-wrapper script
	//define("EWIKI_SCRIPT", "?id=");

        #-- change to your needs (site lang)
	define("EWIKI_NAME", "ErfurtWiki");		# Wiki Title
	//define("EWIKI_PAGE_INDEX", "ErfurtWiki");	# FrontPage
	define("EWIKI_PAGE_NEWEST", "NewestPages");
	define("EWIKI_PAGE_SEARCH", "SearchPages");
	define("EWIKI_PAGE_HITS", "MostVisitedPages");
	define("EWIKI_PAGE_VERSIONS", "MostOftenChangedPages");
	define("EWIKI_PAGE_UPDATES", "UpdatedPages");	# RecentChanges

	#-- default settings are good settings - most often ;)
        #- look & feel
	define("EWIKI_PRINT_TITLE", 1);		# <h2>WikiPageName</h2> on top
	//define("EWIKI_SPLIT_TITLE", 0);		# <h2>Wiki Page Name</h2>
	define("EWIKI_CONTROL_LINE", 1);	# EditThisPage-link at bottom
	define("EWIKI_LIST_LIMIT", 20);		# listing limit
        #- behaviour
	define("EWIKI_AUTO_EDIT", 1);		# edit box for non-existent pages
	define("EWIKI_EDIT_REDIRECT", 1);	# redirect after edit save
	define("EWIKI_DEFAULT_ACTION", "view"); # (keep!)
	define("EWIKI_CASE_INSENSITIVE", 1);	# (not yet implemented)
	define("UNIX_MILLENIUM", 1000000000);
        #- rendering
	define("EWIKI_ALLOW_HTML", 0);		# often a very bad idea
	define("EWIKI_HTML_CHARS", 1);		# allows for &#200;
	define("EWIKI_ESCAPE_AT", 1);		# "@" -> "&#x40;"
	define("EWIKI_FORMAT_PRE_MIN_NL", 4);
	define("EWIKI_FORMAT_PRE_MAX_NL", 5);
	define("EWIKI_FORMAT_PRE_END_NL", 2);
        #- http/urls
	define("EWIKI_HTTP_HEADERS", 1);	# most often a good thing
	define("EWIKI_URLENCODE", 1);		# disable when _USE_PATH_INFO
	define("EWIKI_URLDECODE", 1);
	define("EWIKI_USE_PATH_INFO", 1  &&!strstr($_SERVER["SERVER_SOFTWARE"],"Apache"));
	define("EWIKI_USE_ACTION_PARAM", 1);
	define("EWIKI_UP_PAGENUM", "n");	# _UP_ means "url parameter"
	define("EWIKI_UP_BINARY", "binary");
	define("EWIKI_UP_UPLOAD", "upload");
	#- user permissions
	//define("EWIKI_PROTECTED_MODE", 0);	# disable funcs + require auth
	//define("EWIKI_PROTECTED_MODE_HIDING", 0);  # hides disallowed actions
	//define("EWIKI_AUTH_DEFAULT_RING", 3);	# 0=root 1=priv 2=user 3=view

	#-- allowed WikiPageNameCharacters
	define("EWIKI_CHARS_L", "a-zäöüß_µ¤$");	# allowed characters for/in
	define("EWIKI_CHARS_U", "A-ZÄÖÜ");	# WikiLinks

        #-- database
	//define("EWIKI_DB_TABLE_NAME", "ewiki");      # MySQL / ADOdb
	define("EWIKI_DBFILES_DIRECTORY", "/tmp");   # see "db_flat_files.php"
	define("EWIKI_DBA", "/tmp/ewiki.dba");       # see "db_dba.php"
	define("EWIKI_DBQUERY_BUFFER", 0*1024);    # 512K
	
	define("EWIKI_DB_F_TEXT", 1<<0);
	define("EWIKI_DB_F_BINARY", 1<<1);
	define("EWIKI_DB_F_DISABLED", 1<<2);
	define("EWIKI_DB_F_HTML", 1<<3);
	define("EWIKI_DB_F_READONLY", 1<<4);
	define("EWIKI_DB_F_WRITEABLE", 1<<5);
	define("EWIKI_DB_F_APPENDONLY", 1<<6);  #nyi
	define("EWIKI_DB_F_SYSTEM", 1<<7);
	define("EWIKI_DB_F_TYPE", EWIKI_DB_F_TEXT | EWIKI_DB_F_BINARY | EWIKI_DB_F_DISABLED | EWIKI_DB_F_SYSTEM);
	define("EWIKI_DB_F_ACCESS", EWIKI_DB_F_READONLY | EWIKI_DB_F_WRITEABLE | EWIKI_DB_F_APPENDONLY);
	define("EWIKI_DB_F_COPYMASK", EWIKI_DB_F_TEXT | EWIKI_DB_F_READONLY);

	define("EWIKI_DBFILES_NLR", '\\n');
	define("EWIKI_DBFILES_ENCODE", 0 || (DIRECTORY_SEPARATOR != "/"));
	define("EWIKI_DBFILES_GZLEVEL", "2");

	#-- internal
 	define("EWIKI_ADDPARAMDELIM", (strstr(EWIKI_SCRIPT,"?") ? "&" : "?"));

	#-- binary content (images)
	define("EWIKI_SCRIPT_BINARY", /*"/binary.php?binary="*/  ltrim(strtok(" ".EWIKI_SCRIPT,"?"))."?".EWIKI_UP_BINARY."="  );
	define("EWIKI_CACHE_IMAGES", 1  &&!headers_sent());
	define("EWIKI_IMAGE_MAXSIZE", 64 *1024);
	define("EWIKI_IMAGE_MAXALLOC", 1<<19);
	define("EWIKI_IMAGE_RESIZE", 1);
	define("EWIKI_IDF_INTERNAL", "internal://");
	define("EWIKI_ACCEPT_BINARY", 0);   # for arbitrary binary data files

	#-- misc
	//define("EWIKI_LOGLEVEL", -1);		# 0=error 1=warn 2=info 3=debug
	//define("EWIKI_LOGFILE", "/tmp/ewiki.log");

	#-- plugins (tasks mapped to function names)
	$ewiki_plugins["database"][] = "ewiki_database_mysql";
	$ewiki_plugins["edit_preview"][] = "ewiki_page_edit_preview";
	$ewiki_plugins["render"][] = "ewiki_format";
	$ewiki_plugins["init"][-5] = "ewiki_localization";
	$ewiki_plugins["init"][-1] = "ewiki_binary";
	(EWIKI_CONTROL_LINE) and ($ewiki_plugins["view_append"][-1] = "ewiki_control_links");
        (EWIKI_PRINT_TITLE) and ($ewiki_plugins["view_final"][-1] = "ewiki_print_title");

	#-- internal pages
	$ewiki_plugins["page"][EWIKI_PAGE_NEWEST] = "ewiki_page_newest";
	$ewiki_plugins["page"][EWIKI_PAGE_SEARCH] = "ewiki_page_search";
	$ewiki_plugins["page"][EWIKI_PAGE_HITS] = "ewiki_page_hits";
	$ewiki_plugins["page"][EWIKI_PAGE_VERSIONS] = "ewiki_page_versions";
	$ewiki_plugins["page"][EWIKI_PAGE_UPDATES] = "ewiki_page_updates";

	#-- page actions
	$ewiki_plugins["action"]["edit"] = "ewiki_page_edit";
	$ewiki_plugins["action"]["links"] = "ewiki_page_links";
	$ewiki_plugins["action"]["info"] = "ewiki_page_info";
	$ewiki_plugins["action"]["view"] = "ewiki_page";
	$ewiki_plugins["action"]["fetchback"] = "ewiki_page_edit";

	#-- helper vars
	$ewiki_plugins["idf"]["url"] = array("http://", "https://", "mailto:", "internal://", "ftp://", "irc://", "telnet://", "news://", "chrome://", "file://", "gopher://");
	$ewiki_plugins["idf"]["img"] = array(".jpeg", ".jpg", ".png", ".gif", ".j2k");
	$ewiki_plugins["idf"]["obj"] = array(".swf", ".svg");

	#-- init stuff, autostarted parts
	ksort($ewiki_plugins["init"]);
	if ($pf_a = $ewiki_plugins["init"]) foreach ($pf_a as $pf) {
           $pf($GLOBALS);
        }
	
	#-- text
	$ewiki_t["en"] = @array_merge($ewiki_t["en"], array(
	   "EDITTHISPAGE" => "EditThisPage",
	   "PAGESLINKINGTO" => "Pages linking to",
	   "INFOABOUTPAGE" => "Information about page",
	   "LIKEPAGES" => "Pages like this",
	   "NEWESTPAGES" => "Newest Pages",
	   "LASTCHANGED" => "last changed on %c",
	   "DOESNOTEXIST" => "This page does not yet exist, please click on EditThisPage if you'd like to create it.",
	   "DISABLEDPAGE" => "This page is currently not available.",
	   "ERRVERSIONSAVE" => "Sorry, while you edited this page someone else
		did already save a changed version. Please go back to the
		previous screen and copy your changes to your computers
		clipboard to insert it again after you reload the edit
		screen.",
	   "ERRORSAVING" => "An error occoured while saving your changes. Please try again.",
	   "THANKSFORCONTRIBUTION" => "Thank you for your contribution!",
	   "CANNOTCHANGEPAGE" => "This page cannot be changed.",
	   "OLDVERCOMEBACK" => "Make this old version come back to replace the current one",
	   "PREVIEW" => "Preview",
	   "SAVE" => "Save",
	   "CANCEL_EDIT" => "CancelEditing",
	   "UPLOAD_PICTURE_BUTTON" => "upload picture &gt;&gt;&gt;",
	   "EDIT_FORM_1" => "<a href=\"".EWIKI_SCRIPT."GoodStyle\">GoodStyle</a> is to
		write what comes to your mind. Don't care about how it
		looks too much now. You can add <a href=\"".EWIKI_SCRIPT."WikiMarkup\">WikiMarkup</a>
		also later if you think it is necessary.<br><br>",
	   "EDIT_TEXTAREA_RESIZE_JS" => '<a href="javascript:ewiki_enlarge()" style="text-decoration:none">+</a><script type="text/javascript"><!--'."\n".'function ewiki_enlarge() {var ta=document.getElementById("ewiki_content");ta.style.width=((ta.cols*=1.1)*10).toString()+"px";ta.style.height=((ta.rows*=1.1)*30).toString()+"px";}'."\n".'//--></script>',
	   "EDIT_FORM_2" => "<br>Please do not write things, which may make other
		people angry. And please keep in mind that you are not all that
		anonymous in the internet (find out more about your computers
		'<a href=\"http://google.com/search?q=my+computers+IP+address\">IP address</a>' at Google).",
	   "EDIT_FORM_3" => "",
	   "BIN_IMGTOOLARGE" => "Image file is too large!",
	   "BIN_NOIMG" => "This is no image file (inacceptable file format)!",
	   "FORBIDDEN" => "You are not authorized to access this page.",
	));
	$ewiki_t["de"] = @array_merge($ewiki_t["de"], array(
	   "EDITTHISPAGE" => "DieseSeiteÄndern",
	   "PAGESLINKINGTO" => "Verweise zur Seite",
	   "INFOABOUTPAGE" => "Informationen über Seite",
	   "LIKEPAGES" => "Ähnliche Seiten",
	   "NEWESTPAGES" => "Neueste Seiten",
	   "LASTCHANGED" => "zuletzt geändert am %d.%m.%Y um %H:%M",
	   "DISABLEDPAGE" => "Diese Seite kann momentan nicht angezeigt werden.",
	   "ERRVERSIONSAVE" => "Entschuldige, aber während Du an der Seite
		gearbeitet hast, hat bereits jemand anders eine geänderte
		Fassung gespeichert. Damit nichts verloren geht, browse bitte
		zurück und speichere Deine Änderungen in der Zwischenablage
		(Bearbeiten->Kopieren) um sie dann wieder an der richtigen
		Stelle einzufügen, nachdem du die EditBoxSeite nocheinmal
		geladen hast.<br>
		Vielen Dank für Deine Mühe.",
	   "ERRORSAVING" => "Beim Abspeichern ist ein Fehler aufgetreten. Bitte versuche es erneut.",
	   "THANKSFORCONTRIBUTION" => "Vielen Dank für Deinen Beitrag!",
	   "CANNOTCHANGEPAGE" => "Diese Seite kann nicht geändert werden.",
	   "OLDVERCOMEBACK" => "Diese alte Version der Seite wieder zur Aktuellen machen",
	   "PREVIEW" => "Vorschau",
	   "SAVE" => "Speichern",
	   "CANCEL_EDIT" => "ÄnderungenVerwerfen",
	   "UPLOAD_PICTURE_BUTTON" => "Bild hochladen &gt;&gt;&gt;",
	   "EDIT_FORM_1" => "<a href=\"".EWIKI_SCRIPT."GuterStil\">GuterStil</a> ist es,
		ganz einfach das zu schreiben, was einem gerade in den
		Sinn kommt. Du solltest dich jetzt noch nicht so sehr
		darum kümmern, wie die Seite aussieht. Du kannst später
		immernoch zurückkommen und den Text mit <a href=\"".EWIKI_SCRIPT."FormatierungsRegeln\">WikiTextFormatierungsRegeln</a>
		aufputschen.<br>",
	   "EDIT_FORM_2" => "<br>Bitte schreib keine Dinge, die andere Leute
		verärgern könnten. Und bedenke auch, daß es schnell auf
		dich zurückfallen kann wenn du verschiedene andere Dinge sagst (mehr Informationen zur
		'<a href=\"http://google.de/search?q=computer+IP+adresse\">IP Adresse</a>'
		deines Computers findest du bei Google).",
	));

	#-- entitle actions
	$ewiki_plugins["action_links"]["view"] = @array_merge(array(
		"edit" => ewiki_t("EDITTHISPAGE"),
		"links" => ewiki_t("PAGESLINKINGTO"),
		"info" => ewiki_t("INFOABOUTPAGE"),
		"like" => ewiki_t("LIKEPAGES"),
	), $ewiki_plugins["action_links"]["view"]);
	$ewiki_plugins["action_links"]["info"] = @array_merge(array(
		"view" => "view",
		"fetchback" => "fetchback",
	), $ewiki_plugins["action_links"]["info"]);

	#-- InterWiki:Links
	$ewiki_plugins["interwiki"] = @array_merge(
	$ewiki_plugins["interwiki"],
	array(
           "this" => EWIKI_SCRIPT,  #-- should be absolute url to ewiki wrapper
	   "ErfurtWiki" => "http://erfurtwiki.sourceforge.net/?id=",
          #"url"=>"",
          #"phpwiki" => "this",
	   "Wiki" => "http://www.c2.com/cgi/wiki?",
	   "WardsWiki" => "Wiki",
	   "WikiFind" => "http://c2.com/cgi/wiki?FindPage&value=",
	   "WikiPedia" => "http://www.wikipedia.com/wiki.cgi?",
	   "MeatballWiki" => "http://www.usemod.com/cgi-bin/mb.pl?",
	   "UseMod"       => "http://www.usemod.com/cgi-bin/wiki.pl?",
	   "PhpWiki" => "http://phpwiki.sourceforge.net/phpwiki/index.php3?",
	   "LinuxWiki" => "http://linuxwiki.de/",
	   "OpenWiki" => "http://openwiki.com/?",
	   "Tavi" => "http://andstuff.org/tavi/",
	   "TWiki" => "http://twiki.sourceforge.net/cgi-bin/view/",
	   "MoinMoin" => "http://www.purl.net/wiki/moin/",
	   "Google" => "http://google.com/search?q=",
	   "ISBN" => "http://www.amazon.com/exec/obidos/ISBN=",
	   "icq" => "http://www.icq.com/",
	));





#-------------------------------------------------------------------- main ---


/*  this is the main function, which you should preferrable call to
    integrate the ewiki into any web site; it chains to most other
    parts and plugins (includes the edit box);
    if you do not supply the requested pages "$id" it will fetch it
    from the pre-defined URL parameters
*/
function ewiki_page($id=false) {

   global $ewiki_links, $ewiki_plugins, $ewiki_ring;
   
   #-- output var
   $o = "";

   #-- selected page
   if (!isset($_REQUEST)) {
      $_REQUEST = array_merge($HTTP_GET_VARS, $HTTP_POST_VARS);
   }
   if (!strlen($id)) {
      ($id = @$_REQUEST["id"]) or
      ($id = @$_REQUEST["name"]) or
      ($id = @$_REQUEST["page"]) or
      ($id = @$_REQUEST["file"]) or
      (EWIKI_USE_PATH_INFO) and ($id = ltrim(@$_SERVER["PATH_INFO"], "/")) or
      (!isset($_REQUEST["id"])) and ($id = trim(strtok($_SERVER["QUERY_STRING"], "&")));
      if (!strlen($id) || ($id=="id=")) {
         $id = EWIKI_PAGE_INDEX;
      }
      (EWIKI_URLDECODE) && ($id = urldecode($id));
   }

   #-- page action
   $action = EWIKI_DEFAULT_ACTION;
   if ($delim = strpos($id, "/")) {
      $action = substr($id, 0, $delim);
      $id = substr($id, $delim + 1);
   }
   elseif (EWIKI_USE_ACTION_PARAM && ($uu = $_REQUEST["action"])) {
      $action = $uu;
   }
   $GLOBALS["ewiki_id"] = $id;
   $GLOBALS["ewiki_title"] = ewiki_page_title($id);
   $GLOBALS["ewiki_action"] = $action;

   #-- fetch from db
   $dquery = array(
      "id" => $id
   );
   if (!isset($_REQUEST["content"]) && ($dquery["version"] = @$_REQUEST["version"])) {
      $dquery["forced_version"] = $dquery["version"];
   }
   $data = array_merge($dquery, ewiki_database("GET", $dquery));

   #-- initialize database if much nothing found
   if (($id == EWIKI_PAGE_INDEX) && ($id) && empty($data["content"])) {
      ewiki_initialize();
      $data = ewiki_database("GET", array("id"=>$id));
   }

   #-- stop here if disabled page
   if (!empty($data["flags"]) && (($data["flags"] & EWIKI_DB_F_TYPE) != EWIKI_DB_F_TEXT)) {
      return($o .= ewiki_t("DISABLEDPAGE"));
   }

   #-- edit <form> for non-existent pages
   if (($action == EWIKI_DEFAULT_ACTION) && empty($data["content"]) && empty($ewiki_plugins["page"][$id])) {
      if (EWIKI_AUTO_EDIT) {
         $action = "edit";
      }
      else {
         $data["content"] = ewiki_t("DOESNOTEXIST");
      }
   }

   #-- more initialization
   if ($pf_a = $ewiki_plugins["view_init"]) {
      foreach ($pf_a as $pf) {
         $o .= $pf($id, $data, $action);
      }
   }

   #-- require auth
   if (EWIKI_PROTECTED_MODE) {
      if (!ewiki_auth($id, $data, $action, $ring=false, $force=0)) {
         return($o.=$data);
      }
   }

   #-- internal pages
   if (($pf = @$ewiki_plugins["page"][$id]) && function_exists($pf)) {

      $o .= call_user_func($pf, $id, $data, $action);

   }
   #-- page actions
   elseif (($pf = @$ewiki_plugins["action"][$action]) && ($pf!="ewiki_page") && function_exists($pf)) {

     $o .= call_user_func($pf, $id, $data, $action);

   }
   #-- "view" action
   else {

      if ($_REQUEST["thankyou"]) {
         $o .= ewiki_t("THANKSFORCONTRIBUTION") . $o;
      }

      #-- render requested wiki page  <-- goal !!!
      $o .= $ewiki_plugins["render"][0] ( $data["content"], 1, EWIKI_ALLOW_HTML || (@$data["flags"]&EWIKI_DB_F_HTML) );

      #-- control line + other per-page info stuff
      if ($pf_a = $ewiki_plugins["view_append"]) {
         ksort($pf_a);
         foreach ($pf_a as $n => $pf) { $o .= $pf($id, $data, $action); }
      }
      if ($pf_a = $ewiki_plugins["view_final"]) {
         ksort($pf_a);
         foreach ($pf_a as $n => $pf) { $pf($o, $id, $data, $action); }
      }

      ewiki_database("HIT", $data);
   }

   if (EWIKI_HTTP_HEADERS && ($ver = @$data["version"]) && ($id = @$data["id"])) {
      @header('Content-Disposition: inline; filename="' . urlencode($id) . '.html"');
      @header('Content-Version: ' . $ver);
      @header('Last-Modified: ' . gmstrftime("%a, %d %b %G %T %Z", $data["lastmodified"]));
   }

   (EWIKI_ESCAPE_AT) && ($o = str_replace("@", "&#x40;", $o));

   return($o);
}




function ewiki_page_title ($id='', $split=EWIKI_SPLIT_TITLE) {
   strlen($id) or ($id = $GLOBALS["ewiki_id"]);
   if ($split) {
      $id = preg_replace("/([".EWIKI_CHARS_L."])([".EWIKI_CHARS_U."]+)/", "$1 $2", $id);
   }
   return(htmlentities($id));
}




function ewiki_print_title(&$html, $id, $data, $action) {
   $html = '<h2><a href="' . ewiki_script("links", $id) .
      '">' . $GLOBALS["ewiki_title"] . "</a></h2>\n" . $html;
}




/*  replaces EWIKI_SCRIPT, works more sophisticated, and can be
    used to work around various design flaws
    - if only the first parameter is used (old style), it can contain
      a complete "action/WikiPage" - but this is ambigutious
    - else $asid is the action, and $id contains the WikiPageName
    - $ewiki_script will be used in the future
    - needs more work on _BINARY, should be a separate function
*/
function ewiki_script($asid, $id=false, $params=array(), $bin=0, $html=1) {

   global $ewiki_script, $ewiki_script_binary, $ewiki_plugins;

   #-- create global vars from constants
   if (empty($ewiki_script)) {
      $ewiki_script = EWIKI_SCRIPT;
   }
   if (empty($ewiki_script_binary)) {
      $ewiki_script_binary = EWIKI_SCRIPT_BINARY;
   }

   #-- separate $action and $id for old style requests
   if ($id === false) {
      if (strpos($asid, "/") !== false) {
         $asid = strtok($asid, "/");
         $id = strtok("\000");
      }
      else {
         $id = $asid;
         $asid = "";
      }
   }

   #-- workaround slashes in $id
   if (empty($asid) && (strpos($id, "/") !== false) && !$bin) {
      $asid = "view";
   }
   /*paranoia*/ $asid = trim($asid, "/");

   #-- make url
   if (EWIKI_URLENCODE) {
      $id = urlencode($id);
      $asid = urlencode($asid);
   }
   else {
      # only urlencode &, %, ?
   }
   $url = ($bin ? $ewiki_script_binary : $ewiki_script);
   $id = ($asid ? $asid . "/" : "") . $id;
   if (strpos($url, "%s") !== false) {
      $url = str_replace("%s", $id, $url);
   }
   else {
      $url .= $id;
   }

   #-- add url params
   if (is_string($params)) {
      if (strlen($params)) {
         $url .= (strpos($url,"?")!==false ? "&":"?") . $params;
      }
   }
   else {
      if ($params) foreach ($params as $k=>$v) {
         $url .= (strpos($url,"?")!==false ? "&":"?") . rawurlencode($k) . "=" . rawurlencode($v);
      }
   }
   if ($html) {
      $url = str_replace("&", "&amp;", $url);
   }

   return($url);
}


/*  right now just a wrapper
*/
function ewiki_script_binary($asid, $id=false, $params=array(), $upload=0) {

   $upload |= is_string($params) && strlen($params) || count($params);

   $url = ewiki_script($asid, $id, $params, "_BINARY=1");

   return($url);
}


#------------------------------------------------------------ page plugins ---



function ewiki_page_links($id, $data=0) {

      (EWIKI_PRINT_TITLE) and ($o = '<h4>' . ewiki_t("PAGESLINKINGTO") . ' <a href="' . ewiki_script("", $id) . '">' . $id . "</a></h4>\n");

      $result = ewiki_database("SEARCH", array("refs" => $id));

      $pages = array();
      while ($r = $result->get()) {
         if ( strpos("\n\n\n\n".$r["refs"]."\n\n\n\n", "\n$id\n") ) {
            $pages[] = $r["id"];
         }
      }

      $o .= ewiki_list_pages($pages);

      return($o);
}




function ewiki_list_pages($pages=array(), $limit=EWIKI_LIST_LIMIT,
                          $value_as_title=0, $pf=false)
{
   global $ewiki_plugins;

   $is_num = !empty($pages[0]);
   $lines = array();
   $n = 0;

   foreach ($pages as $pn=>$add_text) {

      $title = $pn;

      if ($value_as_title) {
         $title = $add_text;
         $add_text = "";
      }
      elseif ($is_num) {
         $pn = $title = $add_text;
         $add_text = "";
      }

      $id = strtok($pn, "&");
//@FIXME #-- this is a workaround for the page_wordindex (redirecting to
// PowerSearch, but it breaks page names with a "&" inside in all generated
// lists
      $params = strtok("\000");
      $lines[] = '<a href="' . ewiki_script("", $id, $params) . '">' . $title . '</a> ' . $add_text;

      if (($limit > 0)  &&  ($n++ >= $limit)) {
         break;
      }
   }

   if (($pf) || ($pf = @$ewiki_plugins["list_pages"][0])) {
      $o = $pf($lines);
   }
   elseif($lines) {
      $o = "&middot; " . implode("<br>\n&middot; ", $lines) . "<br>\n";
   }

   return($o);
}




function ewiki_page_ordered_list($orderby="created", $asc=0, $print="%n things", $title="Ordered List") {

      (EWIKI_PRINT_TITLE) and ($o = "<h3>$title</h3>\n");

      $sorted = array();
      $result = ewiki_database("GETALL", array($orderby));

      while ($row = $result->get()) {
         $row = ewiki_database("GET", array(
            "id" => $row["id"],
            ($asc >= 0 ? "version" : "uu") => 1  // ver1 most accurate about {hits}
         ));
         if (EWIKI_DB_F_TEXT == ($row["flags"] & EWIKI_DB_F_TYPE)) {
            $sorted[$row["id"]] = $row[$orderby];
         }
      }

      if ($asc != 0) { arsort($sorted); }
      else { asort($sorted); }

      foreach ($sorted as $name => $value) { 
         if (empty($value)) { $value = "0"; }
         $sorted[$name] = strftime(str_replace('%n', $value, $print), $value);
      }
      $o .= ewiki_list_pages($sorted);
      
      return($o);
}



function ewiki_page_newest($id=0, $data=0) {
   return( ewiki_page_ordered_list("created", 1, ewiki_t("LASTCHANGED"), ewiki_t("NEWESTPAGES")) );
}

function ewiki_page_updates($id=0, $data=0) {
   return( ewiki_page_ordered_list("lastmodified", -1, ewiki_t("LASTCHANGED"), EWIKI_PAGE_UPDATES) );
}

function ewiki_page_hits($id=0, $data=0) {
   return( ewiki_page_ordered_list("hits", 1, "%n hits", EWIKI_PAGE_HITS) );
}

function ewiki_page_versions($id=0, $data=0) {
   return( ewiki_page_ordered_list("version", -1, "%n changes", EWIKI_PAGE_VERSIONS) );
}







function ewiki_page_search($id=0, $data=0) {

   (EWIKI_PRINT_TITLE) and ($o = "<h3>" . $id . "</h3>\n");

   if (! ($q = @$_REQUEST["q"])) {

      $o .= '<form action="' . ewiki_script("", $id) . '" method="POST">';
      $o .= '<input name="q" size="30"><br><br>';
      $o .= '<input type="submit" value="'.$id.'">';
      $o .= '</form>';
   }
   else {
      $found = array();

      $q = preg_replace('/\s*[^\w]+\s*/', ' ', $q);
      foreach (explode(" ", $q) as $search) {

         if (empty($search)) { continue; }

         $result = ewiki_database("SEARCH", array("content" => $search));

         while ($row = $result->get()) {
if (empty($row["id"])) {
print_r($row);
}
            $found[] = $row["id"];
         }
      }

      $o .= ewiki_list_pages($found);
   }
 
   return($o);
}








function ewiki_page_info($id, $data) {

      global $ewiki_plugins, $ewiki_links;

      (EWIKI_PRINT_TITLE) and ($o = '<h4>' . ewiki_t("INFOABOUTPAGE") .
      ' "<a href="' . ewiki_script("", $id) . '">' . $id . "</a>\"</h4>\n");

      $show = array(
         "version", "author", "created",
         "lastmodified", "refs",
         "flags", "meta", "content"
      );

      for ($version=$data["version"],$first=1; $version>=1; $version--,$first=0) {

         $current = ewiki_database("GET", array("id" => $id, "version" => $version));

         if (!strlen(trim($current["id"])) || !$current["version"] || !strlen(trim($current["content"]))) {
            continue;
         }

         $o .= '<table border="1" cellpadding="2" cellspacing="1">' . "\n";

// ADDED JBA: added vertical bar between menu items for clarity.
         #-- additional info-actions
//         $o .= "<tr><td></td><td> ";
         $o .= "<tr><td></td><td>| ";
         foreach ($ewiki_plugins["action_links"]["info"] as $action => $uu) if ($ewiki_plugins["action"][$action]) {
//            $o .= '<a href="' .
//              ewiki_script($action, $id, array("version"=>$current["version"])) .
//              '">' . $action . '</a> ';
            $o .= '<a href="' .
              ewiki_script($action, $id, array("version"=>$current["version"])) .
              '">' . $action . '</a> | ';
         }
// end JBA
         $o .= "</td></tr>\n";

         #-- print pages` meta data
         foreach($show as $i) {

            $value = $current[$i];

            if ($value >= 1000000000) {
               $value = strftime("%c", $value);
            }
            elseif ($i == "content") {
               $value = strlen(trim($value)) . " bytes";
               $i = "content size";
            }
            elseif ($first && ($i == "refs")) {
               $a = explode("\n", trim($value));
               $ewiki_links = ewiki_database("FIND", $a);
               foreach ($a as $n=>$link) {
                  $a[$n] = ewiki_link_regex_callback(array("$link"), "force_noimg");
               }
               $value = implode(", ", $a);
            }
            elseif (strpos($value, "\n") !== false) {
               $value = str_replace("\n", ", ", trim($value));
            }
            elseif ($i == "version") {
               $value = '<a href="' .
                  ewiki_script("", $id, array("version"=>$value)) . '">' .
                  $value . '</a>';
            }
            elseif ($i == "flags") {
               $fstr = ""; foreach (array(1=>"TEXT", 2=>"BIN", 4=>"DISABLED", 8=>"HTML", 16=>"READONLY") as $n => $s) if ($value & $n) $fstr.=$s." ";
               $value = $fstr;
            }

            $o .= '<tr><td valign="top"><b>' . $i . '</b></td>' .
                  '<td>' . $value . "</td></tr>\n";

         }

         $o .= "</table><br>\n";
      }

      return($o);
}






function ewiki_page_edit($id, $data, $action) {

   global $ewiki_links, $ewiki_author, $ewiki_plugins, $ewiki_ring;

   $hidden_postdata = array();

   #-- previous version come back
   if (@$data["forced_version"]) {

      $current = ewiki_database("GET", array("id"=>$id));
      $data["version"] = $current["version"];
      unset($current);

      unset($_REQUEST["content"]);
      unset($_REQUEST["version"]);
   }

   #-- edit hacks
   if ($pf_a = @$ewiki_plugins["edit_hook"]) {
      foreach ($pf_a as $pf) {
         if ($output = $pf($id, $data, $hidden_postdata)) {
            return($output);
         }
      }
   }

   #-- flags: readonly, disabled, non-text, force-auth
   ($flags = @$data["flags"]);
   $flags_wr = ($flags & EWIKI_DB_F_WRITEABLE) ?1:0;
   $flags_ro = ($flags & EWIKI_DB_F_READONLY) ?1:0;
   $flags_txt = (($flags & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) ?1:0;
   if ( !$flags_wr
        && ( !ewiki_auth($id, $data, $action, 2, "FORCE")
             || ($flags_ro) && !($ewiki_ring<=1)
             || !empty($flags) && (!$flags_txt) && !($ewiki_ring<=0)
           )
   ) {
     $o = !is_array($data) ? $data : "\n" . ewiki_t("CANNOTCHANGEPAGE");
     return($o);
   }

   #-- "Edit Me"
   (EWIKI_PRINT_TITLE) and ($o = "<h2>" . ewiki_t("EDITTHISPAGE") . " \"" . $id . "\"</h2>\n");

   #-- preview
   if (isset($_REQUEST["preview"])) {
      $o .= $ewiki_plugins["edit_preview"][0]($data);
   }

   #-- save
   if (isset($_REQUEST["save"])) {

         #-- normalize to UNIX newlines
         $_REQUEST["content"] = str_replace("\015\012", "\012", $_REQUEST["content"]);
         $_REQUEST["content"] = str_replace("\015", "\012", $_REQUEST["content"]);

         #-- check for concurrent version saving
         $error = 0;
         if ((@$data["version"] >= 1) && ($data["version"] != @$_REQUEST["version"]) || (@$_REQUEST["version"] < 1)) {

            $pf = $ewiki_plugins["edit_patch"][0];

            if (!$pf || !$pf($id, $data)) {
               $error = 1;
               $o .= ewiki_t("ERRVERSIONSAVE") . "<br><br>";
            }

         }
         if (!$error) {

            #-- new pages` flags
            if (! ($set_flags = @$data["flags"] & EWIKI_DB_F_COPYMASK)) {
               $set_flags = 1;
            }
            if (EWIKI_ALLOW_HTML) {
               $set_flags |= EWIKI_DB_F_HTML;
            }

            $save = array(
               "id" => $id,
               "version" => @$data["version"] + 1,
               "flags" => $set_flags,
               "content" => $_REQUEST["content"],
               "author" => ewiki_author(),
               "lastmodified" => time(),
               "created" => ($uu=@$data["created"]) ? $uu : time(),
               "meta" => "",
               "hits" => ($uu=@$data["hits"]) ? $uu : "0",
            );

            ewiki_scan_wikiwords($save["content"], $ewiki_links);
            $save["refs"] = "\n\n".implode("\n", array_keys($ewiki_links))."\n\n";


            if (!ewiki_database("WRITE", $save)) {

               $o .= ewiki_t("ERRORSAVING");

            }
            else {
               #-- prevent double saving, when ewiki_page() is re-called
               $_REQUEST = $_GET = $_POST = array();

               $o = ewiki_t("THANKSFORCONTRIBUTION") . "<br><br>";
               $o .= ewiki_page($id);

               if (EWIKI_EDIT_REDIRECT) {
                  $url = ewiki_script("", $id, "thankyou=1", 0, 0);
                  if (EWIKI_HTTP_HEADERS && !headers_sent()) {
                     header("Status: 303 Redirect for GET");
                     header("Location: $url");
                     #header("URI: $url");
                     #header("Refresh: 0; URL=$url");
                  }
                  else {
                     $o .= '<meta http-equiv="Refresh" content="0; URL='.htmlentities($url).'">';
                  }
               }

            }

         }

         //@RECHERCHE
         // header("Reload-Location: " . ewiki_script("", $id, "", 0, 0) );

   }

   #-- Edit <form>
   else {
         #-- previously edited, or db fetched content
         if (@$_REQUEST["content"] || @$_REQUEST["version"]) {
            $data = array(
               "version" => $_REQUEST["version"],
               "content" => $_REQUEST["content"]
            );
         }
         else {
            if (empty($data["version"])) {
               $data["version"] = 1;
            }
            @$data["content"] .= "";
         }

         #-- normalize to DOS newlines
         $data["content"] = str_replace("\015\012", "\012", $data["content"]);
         $data["content"] = str_replace("\015", "\012", $data["content"]);
         $data["content"] = str_replace("\012", "\015\012", $data["content"]);

         $hidden_postdata["version"] = $data["version"];

         #-- edit textarea
         $o .= ewiki_t("EDIT_FORM_1")
             . '<form method="POST" enctype="multipart/form-data" action="'
             . ewiki_script("edit", $id) . '" name="ewiki"'
             . ' accept-charset="ISO-8859-1">' . "\n";

         foreach ($hidden_postdata as $name => $value) {
             $o .= '<input type="hidden" name="'.$name.'" value="'.$value.'">'."\n";
         }

         $o .= '<textarea wrap="soft" id="ewiki_content" name="content" rows="15" cols="70">'
             . htmlentities($data["content"]) . "</textarea>"
             . ewiki_t("EDIT_TEXTAREA_RESIZE_JS")
             . "\n<br>\n"
             . '<input type="submit" name="save" value=" &nbsp; '. ewiki_t("SAVE") . ' &nbsp; ">'."\n"
             . " &nbsp; "
             . '<input type="submit" name="preview" value=" &nbsp; '. ewiki_t("PREVIEW") . ' &nbsp; ">' . "\n"
             . ' &nbsp; <a href="'. ewiki_script("", $id) . '">' . ewiki_t("CANCEL_EDIT") . '</a>'
             . "\n</form>\n"
             . ewiki_t("EDIT_FORM_2");

          #-- pic upload form
          if (EWIKI_SCRIPT_BINARY && EWIKI_UP_UPLOAD && EWIKI_IMAGES_MAXSIZE) {
             $o .= "\n".'<br><form class="box" action='
             . '"'. ewiki_script_binary("", EWIKI_IDF_INTERNAL, "", "_UPLOAD=1") .'"'
             . ' method="POST" enctype="multipart/form-data" target="_upload">'
             . '<input type="file" name="'.EWIKI_UP_UPLOAD.'">'
             . '<input type="hidden" name="'.EWIKI_UP_BINARY.'" value="'.EWIKI_IDF_INTERNAL.'">'
             . '&nbsp;&nbsp;&nbsp;'
             . '<input type="submit" value="'.ewiki_t("UPLOAD_PICTURE_BUTTON").'">'
             . '</form>';
          }

          $o .= ewiki_t("EDIT_FORM_3");
   }

   return($o);
}


function ewiki_page_edit_preview($data) {
   return( "<hr noshade><div align=\"right\">" . ewiki_t("PREVIEW") . "</div><hr noshade><br>"
           . $GLOBALS["ewiki_plugins"]["render"][0]($_REQUEST["content"], 1, EWIKI_ALLOW_HTML || (@$data["flags"]&EWIKI_DB_F_HTML))
           . "<hr noshade><br>"
   );
}







function ewiki_control_links($id, $data) {

   global $ewiki_plugins, $ewiki_ring;
   $action_links = & $ewiki_plugins["action_links"]["view"];
   
   if ( ! is_array($action_links))
   {
      $action_links = (array)$action_links;
   }

   if (!EWIKI_CONTROL_LINE) { 
      $GLOBALS["ewiki_data"] = $data;
      return("");
   }

   $o = "\n".'<br><div align="right" class="controlbox"><hr noshade>'."\n";

   if (@$data["forced_version"]) {

      $o .= '<form action="' . ewiki_script("edit", $id) . '" method="POST">' .
            '<input type="hidden" name="edit" value="old">' .
            '<input type="hidden" name="version" value="'.$data["forced_version"].'">' .
            '<input type="submit" value="' . ewiki_t("OLDVERCOMEBACK") . '"></form> ';
   }
   else {
      foreach ($action_links as $action => $title)
      if (!empty($ewiki_plugins["action"][$action]))
      {
         if (EWIKI_PROTECTED_MODE
              && (   !ewiki_auth($uu, $uu, $action)
                  || EWIKI_PROTECTED_MODE_HIDING && empty($ewiki_ring)
                 )
         ) {
            continue;
         }

         $o .= '<a href="' . ewiki_script($action, $id) .
               '">' . $title . '</a> · ';
      }
   }

   if ($data["lastmodified"] >= UNIX_MILLENIUM) { 
      $o .= '<small>' . strftime(ewiki_t("LASTCHANGED"), @$data["lastmodified"]) . '</small>';
   }

   $o .= "</div>\n";

   return($o);
}





# ============================================================= rendering ===





########  ###   ###  #########  ###  ###   ###  #######
########  ####  ###  #########  ###  ####  ###  #######
###       ##### ###  ###             ##### ###  ###
######    #########  ###  ####  ###  #########  ######
######    #########  ###  ####  ###  #########  ######
###       ### #####  ###   ###  ###  ### #####  ###
########  ###  ####  #########  ###  ###  ####  #######
########  ###   ###  #########  ###  ###   ###  #######




function ewiki_format ($wiki_source, $scan_links=1,
            $html_allowed=EWIKI_ALLOW_HTML, $safe_html=0)
{
   global $ewiki_links, $ewiki_plugins;

   // pre-scan WikiLinks
   if ($scan_links) {
      ewiki_scan_wikiwords($wiki_source, $ewiki_links);
   }

   // formatted output
   $o = "<p>\n";

   // state vars
   $li_o = "";
   $tbl_o = 0;
   $tab_o = 0;
   $pre_o = 0;
   $post = "";
   $empty_lines = 0;

   // plugins
   $pf_source = @$ewiki_plugins["format_source"];
   $pf_tbl = @$ewiki_plugins["format_table"][0];
   $pf_line = @$ewiki_plugins["format_line"];
   $pf_final = @$ewiki_plugins["format_final"];

   #-- config
   $div_indent = '<div style="margin-left:15px;" class="indent">'."\n";
   $wm_whole_line = array(
      "!!!" => "h2",
      "!!" => "h3",
      "!" => "h4",
      "&gt;&gt;" => 'div align="right"',
      ";:" => 'div style="margin-left:15pt;"',
###   "\t" => 'div style="margin-left:20px"',
   );
   $table_defaults = 'cellpadding="2" border="1" cellspacing="0"';
   $syn_htmlentities = array(
      "&" => "&amp;",
      ">" => "&gt;",
      "<" => "&lt;",
      "%%%" => "<br>",
      "\t" => "        ",
   );
   $wm_list = array(
      "-" => array('ul type="square"', "", "li"),
      "*" => array('ul type="circle"', "", "li"),
      "#" => array("ol", "", "li"),
      ":" => array("dl", "dt", "dd"),
      ";" => array("dl", "dt", "dd"),
   );
   $wm_text_style = array(
      "'''''" => array("<b><i>", "</i></b>"),
      "'''" => array("<b>", "</b>"),
      "___" => array("<i><b>", "</b></i>"),
      "''" => array("<em>", "</em>"),
      "__" => array("<strong>", "</strong>"),
      "^^" => array("<sup>", "</sup>"),
      "==" => array("<tt>", "</tt>"),
#     "***" => array("<b><i>", "</i></b>"),
#     "###" => array("<big><b>", "</b></big>"),
      "**" => array("<b>", "</b>"),
      "##" => array("<big>", "</big>"),
      "µµ" => array("<small>", "</small>"),
   );

   $link_regex = "#[!~\#]?(
\[[^<>[\]\n]+\] |
\^[-".EWIKI_CHARS_U.EWIKI_CHARS_L."]{3,} |
([\w\d]{3,}:)?(?:[".EWIKI_CHARS_U."]+[".EWIKI_CHARS_L."]+){2,}[\w\d]* |
(?:[a-z]{2,9}://|mailto:)[^\s\[\]\'\"\)\,<]+ |
\w[-_.+\w]+@(\w[-_\w]+[.])+\w{2,}
)#x";


   // eleminate html
   foreach ($syn_htmlentities as $find=>$replace) {
      $wiki_source = str_replace($find, $replace, $wiki_source);
   }
   unset($syn_htmlentities["&"]);

   #-- pre-processing plugins (working on wiki source)
   if ($pf_source) {
      foreach ($pf_source as $pf) $pf($wiki_source);
   }

   // add a last empty line to get opened tables/lists closed correctly
   $wiki_source = rtrim($wiki_source) . "\n";


   foreach (explode("\n", $wiki_source) as $line) {
 
      $line = rtrim($line);

      $post = "";

      #-- paragraphs
      if (!strlen(trim($line))) {

         $empty_lines++;

         #-- tab markup (indentation)
         while ($tab_o) {
            $o .= "</div>";
            $tab_o--;
         }

         #-- currently in <pre> text
#         if ($pre_o) {
#            if ($empty_lines >= EWIKI_FORMAT_PRE_END_NL) {
#               $o .= "</pre>\n";
#               $pre_o = 0;
#               $empty_lines = 27+EWIKI_FORMAT_PRE_MAX_NL;
#            }
#         } 

         #-- paragraph break
         if (!$pre_o && ($empty_lines <= 1)) {
            $post .= "</p>\n\n<p>";
         }

      }
      else {
         #-- close <pre> paragraph
         if (strpos($line, "&lt;pre&gt;") === 0) {
#-- old (-dev): ($empty_lines >= EWIKI_FORMAT_PRE_MIN_NL) && ($empty_lines <= EWIKI_FORMAT_PRE_MAX_NL)) {
            $o .= "<pre>";
            $line = substr($line, 11);
            $pre_o = 1;
         }
         elseif (strpos($line, "&lt;/pre&gt;") === 0) {
            $o .= "</pre>\n";
            $line = substr($line, 12);
            $pre_o = 0;
         }

         #-- stop counting empty lines
         $empty_lines = 0;

         #-- <hr> line
         if (strpos($line, "----") === 0) {
            $o .= "<hr noshade>\n";
            continue;
         }

         #-- html commyent
         elseif (strpos($line, "&lt;!--") === 0) {
            $o .= "<!-- " . htmlentities(str_replace("--", "__", substr($line, 7))) . " -->\n";
            continue;
         }
      }


      #-- unescape html markup, or tables wiki markup
      if (strlen($line) && ($line[0] == "|")) {

         #-- tables
         if (strlen($line) > 1+strlen(trim($line,"|"))) {
            $line = substr($line, 1, -1);
            if ($pf_tbl) { $pf_tbl($o, $line, $post, $tbl_o); }
            else {
               if (!$tbl_o) {  $o .= "<table " . $table_defaults . ">\n";  }
               $line = "<tr>\n<td>" . str_replace("|", "</td>\n<td>", $line) . "</td>\n</tr>";
            }
            $tbl_o = 1;
         }

         #-- inline <html>
         elseif ($html_allowed) {
            $line = ltrim(substr($line, 1));
            foreach (array_flip($syn_htmlentities) as $find=>$replace) {
               $line = str_replace($find, $replace, $line);
            }
         }
      }
      #-- close table
      elseif ($tbl_o) {
         $o .= "</table>\n";
         $tbl_o = 0;
      }


      #-- non-<pre> text areas
      if (!$pre_o) {

         #-- spaces/tab markup (indentation)
         if ($n_indent = strspn($line, " ")) {
            $n_indent = (int) ($n_indent / 2.65);
         }
         while ($n_indent > $tab_o) { 
            $o .= $div_indent;
            $tab_o++;
         }
         while ($n_indent < $tab_o) { 
            $o .= "</div>\n";
            $tab_o--;
         }

         #-- whole-line wikimarkup
         foreach ($wm_whole_line as $find=>$replace) {
            if (substr($line, 0, strlen($find)) == $find) {
               $line = ltrim(substr($line, strlen($find)));
               $o .= "<$replace>";
               $post = "</" . strtok($replace, " ") . ">" . $post;
            }
         }

         #-- wiki list markup
         if ( strlen($li_o) || strlen($line) && isset($wm_list[@$line[0]]) ) {
            $n = 0;
            $li = "";
            #-- count differences to previous list wikimarkup
            while (strlen($line) && ($li0=$line[0]) && isset($wm_list[$li0])) {
               $li .= $li0;
               $n++;
               $line = substr($line, 1);
            }
            $line = ltrim($line);

            #-- fetch list definition
            if (strlen($li) && ($last_list_i = $li[strlen($li) - 1]))
            list($list_tag, $list_dt0, $list_entry_tag) = $wm_list[$last_list_i];

            #-- output <ul> until new list wikimarkup rule matched
            while (strlen($li_o) < strlen($li)) {
                  $add = $li[strlen($li_o)];
                  $o .= "<" . $wm_list[$add][0] . ">\n";
                  $li_o .= $add;
            }

            #-- close </ul> lists until "$li_o"=="$li" (list wikimarkup state var)
            while (strlen($li_o) > strlen($li)) {
                  $del = $li_o[strlen($li_o) - 1];
                  $o .= "</" . strtok($wm_list[$del][0], " ") . ">\n";
                  $li_o = substr($li_o, 0, strlen($li_o) - 1);
            }

            #-- more work for <dl> lists
            if (!empty($list_dt0)) {        // ":" == $last_list_i
               list($line_dt, $line) = explode(":", $line, 2);
               $o .= "<$list_dt0>$line_dt</$list_dt0>";
               $list_dt0=$last_list_i=false;
            }

            #-- finally enclose current line in <li>...</li>
            if (!empty($line)) {
               $o .=  "<$list_entry_tag>";
               $post = "</$list_entry_tag>" . $post;
            }

            $li_o = $li;
         }

      } #-- only for non-<pre> text


      #-- text style triggers
      foreach ($wm_text_style as $find=>$replace) {
         $n = strlen($find);
         $loop = 20;
         while(($loop--) && (($l = strpos($line, $find)) !== false) && ($r = strpos($line, $find, $l + $n))) {
            $line = substr($line, 0, $l) . $replace[0] .
                    substr($line, $l + strlen($find), $r - $l - $n) .
                    $replace[1] . substr($line, $r + $n);
         }
      }

      #-- call wiki source formatting plugins that work on current line
      if ($pf_line) {
         foreach ($pf_line as $pf) $pf($o, $line, $post);
      }

      #-- add formatted line to page-output
      $o .= $line . $post . "\n";

   }

   #-- close last line
   if ($pre_o--) {
      $o .= "</pre>";
   }
   while ($tab_o--) { 
      $o .= "</div>";
   }
   $o .= "</p>\n";

   #-- international characters
   if (EWIKI_HTML_CHARS) {
      $o = str_replace("&amp;#", "&#", $o);
   }

   echo '<pre>'; echo $link_regex; echo $o; echo '</pre>'; exit;


   #-- finally the link-detection-regex
   #   (impossible to do with the simple string functions)
   $o = preg_replace_callback($link_regex, "ewiki_link_regex_callback", $o);

   #-- call post processing plugins
   if ($pf_final) {
      foreach ($pf_final as $pf) $pf($o);
   }

   return($o);
}






function ewiki_scan_wikiwords(&$wiki_source, &$ewiki_links) {

   $pregex = '/(?<![~!#])
((?:['.EWIKI_CHARS_U.']+['.EWIKI_CHARS_L.']+){2,}[\w\d]*)
|\[ (?:"[^\]\"]+" | \s+ | [^:\]#]+\|)*  ([^\|\"\[\]\#]+)  (?:\s+ | "[^\]\"]+")* [\]\#]
|\^(['.EWIKI_CHARS_L.EWIKI_CHARS_U.']{3,})
|(\w{3,9}:\/\/[^?#\s\[\]\'\"\)\,<]+)
/x';

   preg_match_all($pregex, $wiki_source, $uu);
   $uu = @array_unique(@array_merge($uu[1], $uu[2], $uu[3], $uu[4], @$uu[5]));
   $ewiki_links = ewiki_database("FIND",  $uu);

   #<off>#      $ewiki_links = array_merge($ewiki_links, $ewiki_plugins["page"]);

   unset($ewiki_links[""]);
}






function ewiki_link_regex_callback($uu, $force_noimg=0) {

   global $ewiki_links, $ewiki_plugins, $ewiki_id;

   $str = $uu[0];
   $type = array();
   $states = array();

   #-- link bracket '[' escaped with '!' or '~'
   if (($str[0] == "!") || ($str[0] == "~")) {
      return(substr($str, 1));
   }
   if ($str[0]=="#") {
      $states["define"] = 1;
      $str = substr($str, 1);
   }
   if ($str[0] == "[") {
      $states["brackets"] = 1;
      $str = substr($str, 1, -1);
   }

   #-- explicit title given via [ title | WikiLink ]
   $href = $title = strtok($str, "|");
   if ($uu = strtok("|")) {
      $href = $uu;
   }
   #-- title and href swapped: swap back
   if (strpos("://", $title) || strpos($title, ":") && !strpos($href, ":")) {
      $uu = $title; $title = $href; $href = $uu;
   }
   #-- new entitling scheme [ url "title" ]
   if ((($l=strpos($str, '"')) < ($r=strrpos($str, '"'))) && ($l!==false) ) {
      $title = substr($str, $l + 1, $r - $l - 1);
      $href = substr($str, 0, $l) . substr($str, $r + 1);
   }

   #-- strip spaces
   $spaces_l = ($href[0]==" ") ?1:0;
   $spaces_r = ($href[strlen($href)-1]==" ") ?1:0;
   $title = ltrim(trim($title), "^");
   $href = ltrim(trim($href), "^");
 
   #-- anchors
   $href = str_replace("&amp;", "&", $href);
   $href2 = "";
   if ($p = strpos($href, "#")) {
      $href2 = substr($href, $p);
      $href = substr($href, 0, $p);
   }
   if ($href == ".") {
      $href = $ewiki_id;
   }

   #-- interwiki links
   if (strpos($href, ":") && !strpos($href, "//") && ($p1 = @$ewiki_plugins["interwiki"][$uu=strtok($href, ":")])) {
      $type = array("interwiki", $uu);
      while ($p1_alias = @$ewiki_plugins["interwiki"][$p1]) {
          $type[] = $p1;
          $p1 = $p1_alias;
      }
      if (!strpos("%s", $p1)) {
          $p1 .= "%s";
      }
      $href = str_replace("%s", strtok("\000"), $p1);
   }
   #-- page anchor jumps
   elseif (strlen($href2) && ($href==$ewiki_id) || ($href[0]=="#") && ($href2=$href)) {
      $type = array("jump");
      $str = '<a href="' . htmlentities($href2) . '">' . $title . '</a>';
   }
   #-- page anchor definitions
   elseif ($states["define"]   /*__ ||($href[0]=="#") __*/  ) {
      $type = array("anchor");
      if ($title==$href) { $title="&nbsp;"; }
      $str = '<a name="' . htmlentities(ltrim($href, "#")) . '">' . ltrim($title, "#") . '</a>';
   }
   #-- ordinary internal WikiLinks
   elseif (($ewiki_links === true) || @$ewiki_links[$href] || @$ewiki_plugins["page"][$href]) {
      $type = array("wikipage");
      $str = '<a href="' . ewiki_script("", $href) . htmlentities($href2) .
             '">' . $title . '</a>';
   }
   #-- guess for mail@addresses, convert to URI if
   elseif (strpos($href, "@") && !strpos($href, ":")) {
      $type = array("email");
      $href = "mailto:" . $href;
   }
   #-- not found fallback
   else {
      $type = array("notfound");
      $str = '<b>' . $title . '</b><a href="' .
             ewiki_script("", $href) . '">?</a>';
                   /* "edit" */
   }

   #-- convert standard URLs
   foreach ($ewiki_plugins["idf"]["url"] as $find) if (strpos($href, $find)===0) {

      $type[-2] = "url";
      $type[-1] = strtok($find, ":");

      #-- URL plugins
      if ($pf_a = $ewiki_plugins["link_url"])
      {
         foreach ($pf_a as $pf)
         {
            if ($str = $pf($href, $title)) 
            {
               break 2;
            }
         }
      }
      $meta = $ewiki_links[$href];

      #-- check for image files
      $ext = substr($href, strrpos($href,"."));
      $obj = in_array($ext, $ewiki_plugins["idf"]["obj"]);
      $img = $obj || in_array($ext, $ewiki_plugins["idf"]["img"]);

      #-- internal:// references (binary files)
      if (EWIKI_SCRIPT_BINARY && ((strpos($href, EWIKI_IDF_INTERNAL)===0)  ||
          EWIKI_IMAGE_MAXSIZE && EWIKI_CACHE_IMAGES && $img) )
      {
         $type = array("binary");
         $href = ewiki_script_binary("", $href);
      }

      #-- output html reference
      if (!$img || $force_noimg || !$states["brackets"] || (strpos($href, EWIKI_IDF_INTERNAL) === 0)) {
         $str = '<a href="' . $href . '">' . $title . '</a>';
      }
      #-- img tag
      else {
         $type = array("image");
         if ($meta = unserialize($meta)) {   #-- uploaded images size
            $x = $meta["width"];
            $y = $meta["height"];
         }
         if ($p = strpos('?', $href)) {      #-- width/height given in url
            parse_str(str_replace("&amp;", "&", substr($href, $p)), $meta);
            ($uu = $meta["x"] + $meta["width"]) and ($x = $uu);
            ($uu = $meta["y"] + $meta["height"]) and ($y = $uu);
            if ($scale = $meta["r"] . $meta["scale"]) {
               ($p = strpos($scale, "%")) and ($scale = strpos($scale, 0, $p) / 100);
               $x *= $scale; $y *= $scale;
            }
         }
         $align = array('', ' align="right"', ' align="left"', ' align="center"');
         $align = $align[$spaces_l + 2*$spaces_r];
         $str = ($obj ? '<embed width="70%"' : '<img') . ' src="' . $href . '"' .
                ' alt="' . htmlentities($title) . '"' .
                ($title!=$href ? ' title="' . htmlentities($title) . '"' : "").
		($x && $y ? " width=\"$x\" height=\"$y\"" : "") .
                $align . '>' . ($obj ? '</embed>' : '</img>');
      }

      break;
   }

   #-- icon plugins
   ksort($type);
   if ($pf_a = $ewiki_plugins["link_final"]) {
      foreach ($pf_a as $pf) { $pf($str, $type, $href, $title); }
   }

   return($str);
}




# =========================================================================



#####    ##  ##   ##    ##    #####   ##  ##
######   ##  ###  ##   ####   ######  ##  ##
##  ##   ##  ###  ##  ######  ##  ##  ##  ##
#####    ##  #### ##  ##  ##  ######  ######
#####    ##  #######  ######  ####     ####
##  ###  ##  ## ####  ######  #####     ##
##  ###  ##  ##  ###  ##  ##  ## ###    ##
######   ##  ##  ###  ##  ##  ##  ##    ##
######   ##  ##   ##  ##  ##  ##  ##    ##




function ewiki_binary($break=0) {

   if (!strlen($id = $_REQUEST[EWIKI_UP_BINARY])) {
      return(false);
   }
   if (headers_sent()) die("ewiki-binary configuration error");

   global $ewiki_plugins;

   #-- upload requests
   $upload_file = @$_FILES[EWIKI_UP_UPLOAD];
   $add_meta = array();
   if ($orig_name = @$upload_file["name"]) {
      $add_meta["Content-Location"] = urlencode($orig_name);
      $add_meta["Content-Disposition"] = 'inline; filename="'.urlencode(basename("remote://$orig_name")).'"';
   }

   #-- uploaded image
   if (($id===EWIKI_IDF_INTERNAL) && ($upload_file)) {

      $id = ewiki_binary_save_image($upload_file["tmp_name"], "", $return=0, $add_meta);
      @unlink($upload_file["tmp_name"]);

      if ($id) {
         echo<<<EOF
<html><head><title>File/Picture Upload</title><script language="JavaScript" type="text/javascript"><!--
 opener.document.forms["ewiki"].elements["content"].value += "\\nUPLOADED PICTURE: [$id]\\n";
 window.setTimeout("self.close()", 5000);
//--></script></head><body bgcolor="#FFFFFF" text="#000000">
Your uploaded file was saved as<br>
<b>[$id]</b>.<br><br>
<noscript>Please copy this into the text input field.</noscript></body></html>
EOF
;
      }
   }

   #-- request for contents from the db
   else {

      $data = ewiki_database("GET", array("id" => $id));
      $flags = @$data["flags"];

      if (EWIKI_DB_F_BINARY == ($flags & EWIKI_DB_F_TYPE)) {

         #-- decode meta/headers
         if ($uu = unserialize($data["meta"])) {
            $meta = $uu;
         }
         else foreach (explode("\n", $data["meta"]) as $str) {
            $meta[trim(strtok($str, ":"))] = trim(strtok("\000"));
         }
         foreach ($meta as $hdr=>$val) if (($hdr[0] >= "A") && ($hdr[0] <= "Z")) {
            header($hdr . ": " . $val);
         }

         #-- fetch from binary store
         if ($pf_a = $ewiki_plugins["binary_get"]) {
            foreach ($pf_a as $pf) { $pf($id, $meta); }
         }

         #-- fpassthru
         echo $data["content"];

      }

      #-- fetch & cache requested URL
      elseif (empty($flags) && EWIKI_CACHE_IMAGES) {

            #-- check for standard URLs, to prevent us from serving
            #   evil requests for '/etc/passwd.jpeg' or '../.htaccess.gif'
            if (preg_match('@^\w?(http|ftp|https|ftps|sftp)\w?://@', $id)) {

               #-- generate local copy
               $filename = tempnam("/tmp", "ewiki.local.temp.");
               if (($i = fopen($id, "rb")) && ($o = fopen($filename, "wb"))) {

                  while (!feof($i)) {
                     fwrite($o, fread($i, 65536));
                  }

                  fclose($i);
                  fclose($o);

                  $add_meta = array(
                     "Content-Location" => urlencode($id),
                     "Content-Disposition" => 'inline; filename="'.urlencode(basename($id)).'"'
                  );

                  $result = ewiki_binary_save_image($filename, $id, "RETURN", $add_meta);
               }
            }

            #-- deliver
            if ($result && !$break) {
               ewiki_binary($break=1);
            }
            #-- mark URL as unavailable
            else {
               $data = array(
                  "id" => $id,
                  "version" => 1, 
                  "flags" => EWIKI_DB_F_DISABLED,
                  "lastmodified" => time(),
                  "created" => time(),
                  "author" => ewiki_author("ewiki_binary_cache"),
                  "content" => ""
               );
               ewiki_database("WRITE", $data);
               header("Location: $id");
               ewiki_log("imgcache: did not find '$id', marked in database as DISABLED", 2);
            }
      }

      #-- "we don't sell this!"
      else {
         header("Status: 301 Located SomeWhere Else");
         header("Location: $id");
         header("URI: $id");
      }

   }

   // you should not remove this one, it is really a good idea to use it!
   die();
}






function ewiki_binary_save_image($filename, $id="", $return=0,
$add_meta=array(), $accept_all=EWIKI_ACCEPT_BINARY, $care_for_images=1)
{
   global $ewiki_plugins;

   #-- break on empty files
   if (!filesize($filename)) {
      return(false);
   }

   #-- check for image type and size
   $mime_types = array(
      "application/octet-stream",
      "image/gif",
      "image/jpeg",
      "image/png",
      "application/x-shockwave-flash"
   );
   $ext_types = array(
      "bin", "gif", "jpeg", "png", "swf"
   );
   list($width, $height, $mime_i, $uu) = getimagesize($filename);
   (!$mime_i) && ($mime_i=0) || ($mime = $mime_types[$mime_i]);

   #-- images expected
   if ($care_for_images) {

      #-- mime type
      if (!$mime_i && !$accept_all || !filesize($filename)) {
         ewiki_die(ewiki_t("BIN_NOIMG"), $return);
         return;
      }

      #-- resize image
      if (strpos($mime,"image/")!==false) {
      if ($pf_a = $ewiki_plugins["image_resize"]) {
      foreach ($pf_a as $pf) {
      if (EWIKI_IMAGE_RESIZE && (filesize($filename) > EWIKI_IMAGE_MAXSIZE)) {
         $pf($filename, $mime, $return);
         clearstatcache();
      }}}}

      #-- reject image if too large
      if (strlen($content) > EWIKI_IMAGE_MAXSIZE) {
         ewiki_die(ewiki_t("BIN_IMGTOOLARGE"), $return);
         return;
      }

      #-- again check mime type and image sizes
      list($width, $height, $mime_i, $uu) = getimagesize($filename);
      (!$mime_i) && ($mime_i=0) || ($mime = $mime_types[$mime_i]);

   }
   ($ext = $ext_types[$mime_i]) or ($ext = $ext_types[0]);

   #-- binary files
   if ((!$mime_i) && ($pf = $ewiki_plugins["mime_magic"][0])) {
      if ($tmp = $pf($content)) {
         $mime = $tmp;
      }
   }
   if (!strlen($mime)) {
      $mime = $mime_types[0];
   }

   #-- store size of binary file
   $add_meta["size"] = filesize($filename);
   $content = "";

   #-- handler for (large/) binary content?
   if ($pf_a = $ewiki_plugins["binary_store"]) {
      foreach ($pf_a as $pf) {
         $pf($filename, $id, $add_meta, $ext);
      }
   }

   #-- read file into memory (2MB), to store it into the database
   if ($filename) {
      $f = fopen($filename, "rb");
      $content = fread($f, 1<<21);
      fclose($f);
   }

   #-- generate db file name
   if (empty($id)) {
      $md5sum = md5($content);
      $id = EWIKI_IDF_INTERNAL . $md5sum . ".$ext";
      ewiki_log("generated md5sum '$md5sum' from file content");
   }

   #-- prepare meta data
   $meta = array_merge(array(
      "class" => $mime_i ? "image" : "file",
      "Content-Type" => $mime,
      "Pragma" => "cache",
   ), $add_meta);
   if ($mime_i) {
      $meta["width"] = $width;
      $meta["height"] = $height;
   }

   #-- database entry
   $data = array(
      "id" => $id,
      "version" => "1", 
      "author" => ewiki_author(),
      "flags" => EWIKI_DB_F_BINARY | EWIKI_DB_F_READONLY,
      "created" => time(),
      "lastmodified" => time(),
      "meta" => serialize($meta),
      "content" => &$content,
   );
   
   #-- write if not exist
   $exists = ewiki_database("FIND", array($id));
   if (! $exists[$id] ) {
      $result = ewiki_database("WRITE", $data);
      ewiki_log("saving of '$id': " . ($result ? "ok" : "error"));
   }
   else {
      ewiki_log("binary_save_image: '$id' was already in the database", 2);
   }

   return($id);
}




# =========================================================================


####     ####  ####   ########     ########
#####   #####  ####  ##########   ##########
###### ######  ####  ####   ###   ####    ###
#############        ####        ####
#############  ####   ########   ####
#### ### ####  ####    ########  ####
####  #  ####  ####        ####  ####
####     ####  ####  ###   ####  ####    ###
####     ####  ####  #########    ##########
####     ####  ####   #######      ########




function ewiki_localization() {
   global $ewiki_t, $ewiki_plugins;
   $deflangs = ','.$_ENV["LANGUAGE"] . ','.$_ENV["LANG"] . ",en";
   foreach (explode(",", @$_SERVER["HTTP_ACCEPT_LANGUAGE"].$deflangs) as $l) {
      $l = strtok($l, ";");
      $l = strtok($l, "-"); $l = strtok($l, "_"); $l = strtok($l, ".");
      $l = trim($l);
      $ewiki_t["languages"][] = strtolower($l);
   }
}





function ewiki_t($const, $repl=array()) {
   global $ewiki_t;
   foreach ($ewiki_t["languages"] as $l) {
      if (is_string($r = $ewiki_t[$l][$const]) || ($r = $ewiki_t[$l][strtoupper($const)])) {
         foreach ($repl as $key=>$value) {
            if ($key[0] != '$') {
               $key = '$'.$key;
            }
            $r = str_replace($key, $value, $r);
         }
         return($r);
      }
   }
   return($const);
}






function ewiki_log($msg, $error_type=3) {
   if ((EWIKI_LOGLEVEL >= 0) && ($error_type <= EWIKI_LOGLEVEL)) {
      $msg = time() . " - " .
             $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"] . " - " .
             $_SERVER["REQUEST_METHOD"] . " " . $_SERVER["REQUEST_URI"] . " - " .
             $msg . "\n";
      error_log($msg, 3, EWIKI_LOGFILE);
   }
}




function ewiki_die($msg, $return=0) {
   ewiki_log($msg, 1);
   if ($return) {
      return($GLOBALS["ewiki_error"] = $msg);
   }
   else {
      die($msg);
   }
}






function ewiki_author($defstr="") {

   $author = $GLOBALS["ewiki_author"];
   $remote = $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"];

   (empty($author)) && (
      ($author = $defstr) ||
      ($author = $_SERVER["HTTP_FROM"]) ||	// RFC2068 sect 14.22
      ($author = $_SERVER["PHP_AUTH_USER"])
   );

   (empty($author))
      && ($author = $remote)
      || ($author = addslashes($author) . " (" . $remote . ")" );

   return($author);
}



/*  returns a value of (true) if the currently logged in user (this must
    be handled by one of the plugin backends) is authenticated to do the
    current $action, or to view the current $id page;
    alternatively just checks current authentication $ring permission level
*/
function ewiki_auth($id, &$data, $action, $ring=false, $request_auth=0) {

   global $ewiki_plugins, $ewiki_ring, $ewiki_author;
   $ok = true;

   if (EWIKI_PROTECTED_MODE) {

      #-- request user authentication
      if ($request_auth && !isset($ewiki_ring)) {	#-- || !isset($ewiki_author))) {
         $ewiki_ring = EWIKI_AUTH_DEFAULT_RING;
         if ($pf_query = $ewiki_plugins["auth_query"][0]) {
            $pf_query($data, $ewiki_author, $ewiki_ring, 0);
         }
      }

      #-- check permission for current request (page/action/ring)
      if ($pf_perm = $ewiki_plugins["auth_perm"][0]) {

         $ok = $pf_perm($id, $data, $action, $ring);

         #-- (re)request user name & password
         if (!$ok && $pf_query && $request_auth && !isset($ewiki_author)) {
            $pf_query($data, $ewiki_author, $ewiki_ring, "FORCE");
         }
      }
      else {
         $ok = (($ring===false) || ($ewiki_ring <= $ring));
      }
   }

   if (!ok) {
      if (is_array($data)) {
         $data = ewiki_t("FORBIDDEN");
      }
   }

   return($ok);
}



/*  reads all files from "./init-pages/" into the database,
    when ewiki is run for the very first time
*/
function ewiki_initialize() {
   ewiki_database("INIT");
//   if ($dh = @opendir($path = "./init-pages")) {
// ADDED JBA: allows ewiki to live outside of local directory
   if ($dh = @opendir($path = EWIKI_BASE_DIR . "init-pages")) {
// end JBA
      while ($filename = readdir($dh)) {
         if (preg_match('/^([A-ZÄÖÜ]+[a-zäöüß]+\w*)+/', $filename)) {
            $found = ewiki_database("FIND", array($filename));
            if (! $found[$filename]) {
               $content = implode("", file("$path/$filename"));
               ewiki_scan_wikiwords($content, $ewiki_links);
               $refs = "\n\n" . implode("\n", array_keys($ewiki_links)) . "\n\n";
               $save = array(
                  "id" => "$filename",
                  "version" => "1",
                  "flags" => "1",
                  "content" => $content,
                  "author" => ewiki_author("ewiki_initialize"),
                  "refs" => $refs,
                  "lastmodified" => filemtime("$path/$filename"),
                  "created" => filectime("$path/$filename")   // (not exact)
               );
               ewiki_database("WRITE", $save);
            }
         }
      }
      closedir($dh);
   }
}




#---------------------------------------------------------------------------



########     ###    ########    ###    ########     ###     ######  ########
########     ###    ########    ###    ########     ###     ######  ########
##     ##   ## ##      ##      ## ##   ##     ##   ## ##   ##    ## ##
##     ##   ## ##      ##      ## ##   ##     ##   ## ##   ##    ## ##
##     ##  ##   ##     ##     ##   ##  ##     ##  ##   ##  ##       ##
##     ##  ##   ##     ##     ##   ##  ##     ##  ##   ##  ##       ##
##     ## ##     ##    ##    ##     ## ########  ##     ##  ######  ######
##     ## ##     ##    ##    ##     ## ########  ##     ##  ######  ######
##     ## #########    ##    ######### ##     ## #########       ## ##
##     ## #########    ##    ######### ##     ## #########       ## ##
##     ## ##     ##    ##    ##     ## ##     ## ##     ## ##    ## ##
##     ## ##     ##    ##    ##     ## ##     ## ##     ## ##    ## ##
########  ##     ##    ##    ##     ## ########  ##     ##  ######  ########
########  ##     ##    ##    ##     ## ########  ##     ##  ######  ########




/*  wrapper
*/
function ewiki_database($action, $args=array(), $sw1=0, $sw2=0, $pf=false) {

   #-- normalize (fetch bad parameters)
   if (($action=="GET") && !is_array($args) && is_string($args)) {   
      $args = array("id" => $args);
   }

   #-- treat special
   if ($action=="GETALL") {
      $args = array_unique(array_merge($args, array("flags", "version")));
      $args = array_diff($args, array("id"));
   }
   elseif ($action=="SEARCH") {
      unset($keys["version"]);
      unset($keys["flags"]);
   }

   #-- database plugin
   if (($pf) || ($pf = @$GLOBALS["ewiki_plugins"]["database"][0])) {
      $r = $pf($action, $args, $sw1, $sw2);
   }
   else {
      ewiki_log("DB layer: no backend!", 0);
      $r = false;
   }

   #-- database layer generation 2 abstraction
   if (is_array($r) && (($action=="SEARCH") || ($action=="GETALL"))) {
      $z = new ewiki_dbquery_result(array_keys($args));
      foreach ($r as $id=>$row) {
         $z->entries[] = $row["id"];
      }
      $r = $z;
   }

   return($r);
}



/*  returned for SEARCH and GETALL queries, as those operations are
    otherwise too memory exhaustive
*/
class ewiki_dbquery_result {

   var $keys = array();
   var $entries = array();
   var $buffer = EWIKI_DBQUERY_BUFFER;
   var $size = 0;

   function ewiki_dbquery_result($keys) {
      $keys = array_merge($keys, array(-50=>"id", "version", "flags"));
      $this->keys = array_unique($keys);
   }

   function add($row) {
      if (is_array($row)) {
         if ($this->buffer) {
            $this->size += strlen(serialize($row));
            $this->buffer = $this->size <= EWIKI_DBQUERY_BUFFER;
         }
         else {
            $row = $row["id"];
         }
      }
      $this->entries[] = $row;
   }

   function get($all=0) {
      $row = array();
      if (count($this->entries)) {

         #-- fetch very first entry from $entries list
         foreach ($this->entries as $i=>$r) {
            unset($this->entries[$i]);
            break;
         }

         #-- finish if buffered entry
         if (is_array($r) && !$all) {
            $row = $r;
         }
         #-- else refetch complete entry from database
         else {
            if (is_array($r)) {
               $r = $r["id"];
            }
            $r = ewiki_database("GET", array("id"=>$r));
            if (!$all) {
               foreach ($this->keys as $key) {
                  $row[$key] = $r[$key];
               }
            }
         }
         unset($r);
      }
      else { $row = false; }

      return($row);
   }

   function count() {
      return(count($this->entries));
   }
}



/*  MySQL database backend
    (default)
*/
function ewiki_database_mysql($action, $args, $sw1, $sw2) {

   #-- reconnect to the database (if multiple are used)
   #<off>#  mysql_ping($GLOBALS["db"]);

   #-- result array
   $r = array();
   
   if (($action=="FIND") && ! is_array($args))
   {   
      $args = array($args);
   }

   switch($action) {

      /*  Returns database entry as array for the page whose name was given
          with the "id" key in the $args array, usually fetches the latest
          version of a page, unless a specific "version" was requested in
          the $args array.
      */
      case "GET":
         $id = "'" . mysql_escape_string($args["id"]) . "'";
         ($version = 0 + @$args["version"]) and ($version = "AND (version=$version)") or ($version="");
         $isql = "SELECT * FROM " . EWIKI_DB_TABLE_NAME
            . " WHERE (pagename=$id) $version  ORDER BY version DESC  LIMIT 1";
         $result = mysql_query($isql);
         if ($result && ($r = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $r["id"] = $r["pagename"];
            unset($r["pagename"]);
         }
         break;

      /*  Increases the hit counter for the page name given in $args array
          with "id" index key.
      */
      case "HIT":
         mysql_query("UPDATE " . EWIKI_DB_TABLE_NAME . " SET hits=(hits+1) WHERE pagename='" . mysql_escape_string($args["id"]) . "'");
         break;

      /*  Stores the $data array into the database, while not overwriting
          existing entries (using WRITE); returns 0 on failure and 1 if
          saved correctly.
      */
      case "OVERWRITE":		// fall-through
         $COMMAND = "REPLACE";

      case "WRITE":
         $args["pagename"] = $args["id"];
         unset($args["id"]);

         $sql1 = $sql2 = "";
         foreach ($args as $index => $value) {
            if (is_int($index)) {
               continue;
            }
            $a = ($sql1 ? ', ' : '');
            $sql1 .= $a . $index;
            $sql2 .= $a . "'" . mysql_escape_string($value) . "'";
         }

         strlen(@$COMMAND) || ($COMMAND = "INSERT");

         $result = mysql_query("$COMMAND INTO " . EWIKI_DB_TABLE_NAME .
            " (" . $sql1 . ") VALUES (" . $sql2 . ")"
         );

         return($result && mysql_affected_rows() ?1:0);
         break;



      /*  Checks for existance of the WikiPages whose names are given in
          the $args array. Returns an array with the specified WikiPageNames
          associated with values of "0" or "1" (stating if the page exists
          in the database). For images/binary db entries returns the "meta"
          field instead of an "1".
      */
      case "FIND":
         $sql = "";
         foreach (array_values($args) as $id) if (strlen($id)) {
            $r[$id] = 0;
            $sql .= ($sql ? " OR " : "") .
                    "(pagename = '" . mysql_escape_string($id) . "')";
         }
         $result = mysql_query($sql = "SELECT pagename AS id, meta FROM " .
            EWIKI_DB_TABLE_NAME . " WHERE $sql "
         );
         while ($result && ($row = mysql_fetch_row($result))) {
            $r[$row[0]] = strpos($row[1], 's:5:"image"') ? $row[1] : 1;
         }
         break;



      /*  Returns an array of __all__ pages, where each entry is made up
          of the fields from the database requested with the $args array,
          e.g. array("flags","meta","lastmodified");
      */
      case "GETALL":
         $result = mysql_query("SELECT pagename AS id,".
            " MAX(version) AS last_version, " . implode(", ", $args) .
            " FROM ". EWIKI_DB_TABLE_NAME. " GROUP BY id");

         $r = new ewiki_dbquery_result($args);
         while ($result && ($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $r->add($row);
         }
         break;



      /*  Returns array of database entries (also arrays), where the one
          specified column matches the specified content string, for example
          $args = array("content" => "text...piece")
          usually only searches in latest versions of all existing pages         
      */
      case "SEARCH":
         $field = implode("", array_keys($args));
         $content = strtolower(implode("", $args));
         if ($field == "id") { $field = "pagename"; }

         $result = mysql_query("SELECT pagename AS id,
            MAX(version) AS version, flags" .
            (EWIKI_DBQUERY_BUFFER ? ", $field" : "") .
            " FROM " . EWIKI_DB_TABLE_NAME .
            " WHERE LOCATE('" . mysql_escape_string($content) .
            "', LCASE($field))  GROUP BY id"
         );

         $r = new ewiki_dbquery_result(array("id","version",$field));
         while ($result && ($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
            $r->add($row);
         }
         break;



      case "DELETE":
         $id = mysql_escape_string($args["id"]);
         $version = $args["version"];
         mysql_query("DELETE FROM " . EWIKI_DB_TABLE_NAME ."
            WHERE pagename='$id' AND version=$version");
         break;



      case "INIT":
         mysql_query("CREATE TABLE " . EWIKI_DB_TABLE_NAME ."
            (pagename VARCHAR(160) NOT NULL,
            version INTEGER UNSIGNED NOT NULL DEFAULT 0,
            flags INTEGER UNSIGNED DEFAULT 0,
            content MEDIUMTEXT,
            author VARCHAR(100) DEFAULT 'ewiki',
            created INTEGER UNSIGNED DEFAULT ".time().",
            lastmodified INTEGER UNSIGNED DEFAULT 0,
            refs MEDIUMTEXT,
            meta MEDIUMTEXT,
            hits INTEGER UNSIGNED DEFAULT 0,
            PRIMARY KEY id (pagename, version) )
            ");
         echo mysql_error();
         break;

      default:
   }

   return($r);
}



?>