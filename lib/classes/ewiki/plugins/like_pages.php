<?php

 # LikePages like in WardsWiki
 # another form of search function, only useful on very huge wikis


 $ewiki_plugins["action"]["like"] = "ewiki_page_like";


 function ewiki_page_like($id, $data=array()) {

    preg_match_all("/([".EWIKI_CHARS_U."][".EWIKI_CHARS_L."]+)/", $id, $words);

    $pages = array();
    foreach ($words[1] as $find) {

       $result = ewiki_database("SEARCH", array("id" => $find));
       while ($row = $result->get()) {

          $pages[$row["id"]] = "";

       }

    }

    $o = "<h3>Pages like »".$id."«</h3>";
    $o .= ewiki_list_pages($pages, 0);
    return($o);
 }


?>