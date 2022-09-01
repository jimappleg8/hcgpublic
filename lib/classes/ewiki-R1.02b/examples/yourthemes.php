<?php

/*
  This example wrapper uses ordinary WikiPages as layout themes. The saved
  ThemePages must contain an (incomplete) <html> page with the string
  '$CONTENT' somewhere in it. Besides the $CONTENT there should also be
  a $TITLE in such Layout pages.
  At the bottom of every page a list of other themes will get appended.
  (this is again just an example, and not meant to be bullet-proof)
*/

 #-- themes
 ($theme = @$_REQUEST["theme"] . @$_REQUEST["skin"])
 or ($theme = "DefaultTheme");

 #-- load ewiki lib
 define("EWIKI_SCRIPT", "yourthemes.php?theme=".$theme."&page=");
 include("config.php"); // db init, ewiki lib

 #-- build page
 $CONTENT = ewiki_page();
 $TEMPLATE = ewiki_db::GET($theme);
 $TEMPLATE = $TEMPLATE["content"];
 if (!strpos($TEMPLATE, '$CONTENT') || !stristr($TEMPLATE, "</body>") || !stristr($TEMPLATE, "<html>")) {
    $TEMPLATE = '<!--FallBackTheme--><html><head><title>$TITLE</title></head><body bgcolor="#ffffff">$CONTENT</body></html>';
 }
 $TEMPLATE = str_replace('$TITLE', $ewiki_title, $TEMPLATE);
 $TEMPLATE = str_replace('$CONTENT', $CONTENT, $TEMPLATE);

 #-- build list of alternative ThemePages
 $list = array();
 $result = ewiki_db::SEARCH("content",'$CONTENT');
 while ($row = $result->get()) {
    if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {
       $list[] = "<a href=\"yourthemes.php?theme={$row['id']}&id=$ewiki_id\">{$row['id']}</a>";
    }
 }
 $list = '<div color="background-color:#000000;color:#ffffff;"><b>YourThemes</b>: '. implode(" | ", $list) .'</div>';
 $TEMPLATE = preg_replace('#(</body>)#i', "\n$list\n\$1", $TEMPLATE);

 #-- print page
 echo $TEMPLATE;

?>