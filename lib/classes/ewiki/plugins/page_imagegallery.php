<?php

 # this plugins brings together all uploaded/cached images onto one
 # page (this usually includes [refrenced] graphics), see internal://
 # CSS: td.lighter { ... }


define("EWIKI_PAGE_IMAGEGALLERY", "ImageGallery");
$ewiki_plugins["page"][EWIKI_PAGE_IMAGEGALLERY] = "ewiki_page_image_gallery";


function ewiki_page_image_gallery($id, $data=0, $action) {

   (EWIKI_PRINT_TITLE) && ($o = "<h3>$id</h3>\n");

   $mwidth = 120;
   $mscale = 0.7;

   #-- fetch and asort images
   $sorted = array();
   $pages = array();
   $result = ewiki_database("GETALL", array("flags", "created", "meta"));
   while ($row = $result->get()) {
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_BINARY) {
         $sorted[$row["id"]] = $row["created"];
         $pages[$row["id"]] = $row;
      }
   }
   arsort($sorted);

   #-- start table
   $o .= '<table border="0" cellpadding="10" cellspacing="4">' . "\n";
   $n = 0;
   $num_per_row = 5;
   foreach ($sorted as $image => $uu) {

      $row = $pages[$image];
      $meta = unserialize($row["meta"]);

      #-- height, width
      $x = $x0 = $meta["width"];
      $y = $y0 = $meta["height"];
      if (! ($x && $y)) {
         $x = $mwidth;
         $y = (int) ($mwidth * $mscale);
      }
      $r = 1;
      if ($y > $mwidth * $mscale) {
         $r = $mwidth * $mscale / $y;
      }
      if ($r > $mwidth / $x) {
         $r = $mwidth / $x;
      }
      $x = (int) ($x * $r);
      $y = (int) ($y * $r);

      #-- get image references
      $ref = array();
      $result = ewiki_database("SEARCH", array("refs"=>$image));
      while ($r = $result->get()) {
         $ref[] = '<a href="' . ewiki_script("", $r["id"]) . '">' . $r["id"] . '</a>';
         if (count($ref) >= 5) {
            break;
         }
      }
      $ref = implode(", ", $ref);

      #-- table lines
      (($n % $num_per_row) == 0) && ($o .= "<tr>\n");

      #-- print a/img tag
      $o .= '<td class="lighter" align="center">'
          . '<a href="' . ewiki_script_binary("", $image) . '">'
          . '<img src="' . ewiki_script_binary("", $image)
          . '" alt="' . $image . '" border="0"'
          . ($x && $y ? ' width="'.$x.'" height="'.$y.'"' : '')
          . '></a><br>'
          . ($x0 && $y0 ? "{$x0}x{$y0}<br>" : "")
          . $ref
          . "</td>\n";

      #-- table lines
      $n++;
      (($n % $num_per_row) == 0) && ($o .= "</tr>\n");

   }

   #-- empty table cells
   if ($n % $num_per_row) {
      while (($n % $num_per_row) && ($n++)) {
         $o .= "<td class=\"lighter\">&nbsp;</td>\n";
      }
      $o .= "</tr>\n";
   }
   $o .= "</table>\n";

   return($o);
}

?>