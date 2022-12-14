<?php

 # this plugins provides the internal page "PageIndex", which lists all
 # pages alphabetically


define("EWIKI_PAGE_PAGEINDEX", "PageIndex");
$ewiki_plugins["page"][EWIKI_PAGE_PAGEINDEX] = "ewiki_page_index";


function ewiki_page_index($id=0, $data=0, $action=0, $args=array()) {

   global $ewiki_plugins;

   (EWIKI_PRINT_TITLE) && ($id) && ($o = "<h3>$id</h3>\n");

   $sorted = array();
   $sorted = array_merge($sorted, array_keys($ewiki_plugins["page"]));

   $exclude = "\n" . implode("\n",
      preg_split("/\s*[,;:\|]\s*/", $args["exclude"])) .
      "\n";

   $result = ewiki_database("GETALL", array("flags"));
   while ($row = $result->get()) {
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {
         if (!stristr($exclude, "\n".$row["id"]."\n")) {
            $sorted[] = $row["id"];
         }
      }
   }

   natcasesort($sorted);

   $o .= ewiki_list_pages($sorted, 0, 0, $ewiki_plugins["list_dict"][0]);

   return($o);
}


?>