<?php

 # this plugins provides the internal page PowerSearch, which allows
 # to search in page contents and/or titles (or for author names, if any),
 # it tries to guess how good the database match matches the requested
 # search strings and orders results,
 # top 10 results are printed more verbosely


define("EWIKI_PAGE_POWERSEARCH", "PowerSearch");
$ewiki_plugins["page"][EWIKI_PAGE_POWERSEARCH] = "ewiki_page_powersearch";



function ewiki_page_powersearch ($id, $data) {

   (EWIKI_PRINT_TITLE) && ($o = "<h3>$id</h3>\n");

   $q = strtolower(preg_replace('/\s*[^\w]+\s*/', ' ', @$_REQUEST["q"]));
   $where = preg_replace('/[^a-z]/', '', @$_REQUEST["where"]);

   if (empty($q) || empty($where)) {

      $o .= '<div class="lighter">
<form action="' . ewiki_script("", $id) . '" method="POST">
<input name="q" size="30">
in <select name="where"><option value="content">page texts</option><option value="id">titles</option><option value="author">author names</option></select>
<br><br>
<input type="submit" value=" &nbsp; &nbsp; S E A R C H &nbsp; &nbsp; ">
</form></div>';

   }
   else {
      $found = array(); 
      $scored = array(); 

      #-- initial scan
      foreach (explode(" ", $q) as $search) {

         if (empty($search)) {
            continue;
         }

         $result = ewiki_database("SEARCH", array($where => $search));

         while ($row = $result->get()) {

            if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {

               $id = $row["id"];
               $content = strtolower($row[$where]);
               unset($row);

               #-- have a closer look
               $len1 = strlen($content) + 1;

               if (!isset($scored[$id])) {
                  $scored[$id] = 1;
               }
               $scored[$id] += 800 * (strlen($search) / $len1);
               $scored[$id] += 65 * (count(explode($search, $content)) - 2);
               $p = -1;
               while (($p = strpos($content, $search, $p+1)) !== false) {
                  $scored[$id] += 80 * (1 - $p / $len1);
               }

            }#if-TXT
         }
      }


      #-- output results
      arsort($scored);
      $o .= "<ol>\n";
      $n = 0;
      foreach ($scored as $id => $score) {

         $o .= "<li>\n";

         $o .= '<div class="lighter">'
             . '<a href="' . ewiki_script("", $id) . '">' . $id . "</a> "
#<off>#      . "<small><small>(#$score)</small></small>"
             . "\n";
 
         if ($n++ < 10) {
            $data = ewiki_database("GET", array("id" => $id));
            preg_match_all('/([_-\w]+)/', $data["content"], $uu);
            $text = htmlentities(substr(implode(" ", $uu[1]), 0, 200));
            $o .= "<br>\n<small>$text\n"
                . "<br>" . strftime(ewiki_t("LASTCHANGED"), $data["lastmodified"])
                . "<br><br></small>\n";
         }

         $o .= "</div>\n";

         $o .= "</li>\n";

      }
      $o .= "</ol>\n";

   }
 
   return($o);
}


?>