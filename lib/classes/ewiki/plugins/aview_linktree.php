<?php

# generates a page tree from the currently viewed page up to
# the index page; and prints it below the EditThisPage-line


define("EWIKI_LINKTREE_UL", 0);		// else a link::list will be printed


#-- register
$ewiki_plugins["view_append"][] = "ewiki_view_append_linktree";




#-- plugin func
function ewiki_view_append_linktree($id, $data, $action) {

   $refs = ewiki_database("GETALL", array("refs"));
   $refs = ewiki_f_parent_refs($refs);

   $depth = 0;
   $paths = array($id=>$id);
   $current = $id;
   $dest = EWIKI_PAGE_INDEX;
/*
 *   $paths["Current"] = "Current";
 *   $paths["WorldWideWeb\nWikiWikiWeb\nErfurtWiki"] = "ErfurtWiki";
 */

   #-- retry until at least one $path is found
   while ( (!in_array($dest, $paths)) && ($depth <= 20) ) {

      $depth++;

      #-- expand every last path entry
      foreach ($paths as $pathkey=>$uu) {

         #-- mk subkey from pathkey
         if ($p = strrpos($pathkey, "\n")) {
            $lkey = substr($pathkey, $p+1);
         }
         else {
            $lkey = $pathkey;
         }

         #-- append tree leafs
         if ($walk = $refs[$lkey]) { 
            foreach ($walk as $add=>$uu) {
               $paths[$pathkey."\n".$add] = $add;
            }
         }
      }   
   }

   #-- print results
   foreach ($paths as $key => $name) {
      if ($name == $dest) {
         if (EWIKI_LINKTREE_UL) {
            $o .= ewiki_f_tree(array_reverse(explode("\n", $key)), 0);
         }
         else {
            $o .= ewiki_f_tree2(array_reverse(explode("\n", $key)), 0);
         }
      }
   }

   ($o) && ($o = "<div class=\"wiki_linktree\">$o</div>\n");


   return($o);
}


#-- outputs the given pages in a treelist
function ewiki_f_tree($pages, $n=1) {

   if ($id = $pages[0]) {

      $o .= "<ul>";
      $o .= ($n ? "<li>" : "") .
            '<a href="'.ewiki_script("",$id).'">'.$id.'</a>' .
            ($n ? "</li>" : "") . "\n";
      $o .= ewiki_f_tree(array_slice($pages, 1));
      $o .= "</ul>\n";
   }

   return($o);
}


#-- outputs a flat link list
function ewiki_f_tree2($pages, $n=1) {

   foreach ($pages as $id) {
      $o[] = '<a href="'.ewiki_script("",$id).'">'.$id.'</a>';
   }

   // "::" instead of "&rarr;" may also look nice
   return(implode(" &rarr; ", $o) . "<br>");
}



#-- build parents array of (reverse) string $refs from the database
function ewiki_f_parent_refs($refs) {

   $pages = array();

   #-- decode refs
   foreach ($refs as $row) {
      $parent = $row["id"];

      foreach (explode("\n", $row["refs"]) as $page) {

         if (strlen($page)) {
            $pages[$page][$parent]=1;
         }

      }
   }

   return($pages);
}


?>