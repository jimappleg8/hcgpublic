<?php

#  lists all pages, which are not referenced from others
#


define("EWIKI_PAGE_ORPHANEDPAGES", "OrphanedPages");
$ewiki_plugins["page"][EWIKI_PAGE_ORPHANEDPAGES] = "ewiki_page_orphanedpages";


function ewiki_page_orphanedpages($id, $data, $action) {

   global $ewiki_links;

   (EWIKI_PRINT_TITLE) and ($o = "<h2>". ewiki_page_title($id) ."</h2>");

   $pages = array();
   $refs = array();
   $orphaned = array();

   #-- read database
   $db = ewiki_database("GETALL", array("refs", "flags"));
   while ($row = $db->get()) {

      $p = $row["id"];

      $rf = $row["refs"];    #-- remove self-reference
      $rf = str_replace("\n$p\n", "\n", $rf);

      $rf = explode("\n", trim($rf));
      $refs = array_merge($refs, $rf);

      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {

         $pages[] = $row["id"];

      }

   }


   #-- check pages to be referenced from somewhere
   foreach ($pages as $p) {

      if (!in_array($p, $refs)) {

         $orphaned[] = $p;

      }
   }

   #-- output
   $o .= ewiki_list_pages($orphaned, 0);

   return($o);
}


?>