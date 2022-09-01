<?php

# Lists all used words inside WikiPageNames and shows a list of them
# (similar to PageIndex) - but it redirects the words to PowerSearch,
# which also needs to be installed therefor!


define("EWIKI_PAGE_WORDINDEX", "WordIndex");
$ewiki_plugins["page"][EWIKI_PAGE_WORDINDEX] = "ewiki_page_wordindex";


function ewiki_page_wordindex($id, $data, $action) {

   global $ewiki_plugins;

   (EWIKI_PRINT_TITLE) && ($o = "<h3>$id</h3>\n");

   $src = "";

   $result = ewiki_database("GETALL", array("flags"));
   while ($row = $result->get()) {
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {
         $src .= " " . $row["id"];
      }
   }
   $src = ewiki_page_title($src, "SPLIT");
   $chars = strtr(EWIKI_CHARS_U.EWIKI_CHARS_L, "_", " ");
   $src = preg_replace("/[^$chars]/", " ", $src);
   $src = explode(" ", $src);
   $src = array_unique($src);
   unset($src[""]);

   natcasesort($src);

   $sorted = array();
   foreach ($src as $i => $word) {

      if (strlen($word) >= 2) {

         $id = EWIKI_PAGE_POWERSEARCH . EWIKI_ADDPARAMDELIM .
               'q=' . ($word) . '&where=id';
         $sorted[$id] = $word;
      }
   }
   unset($src);

   $pf_list_pages = $ewiki_plugins["list_dict"][0];
   $o .= ewiki_list_pages($sorted, $limit=0, $vat=1, $pf_list_pages);

   return($o);

}


 ?>