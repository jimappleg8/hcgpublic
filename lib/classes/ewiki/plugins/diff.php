<?php

 # this is the "stupid diff", which shows up changes between two
 # saved versions of a WikiPage; even if working very unclean it
 # allows to see what has changed
 # it is accessible through the "info about page" action



 $ewiki_plugins["action"]["diff"] = "ewiki_page_stupid_diff";
 $ewiki_plugins["action_links"]["info"]["diff"] = "diff";



 function ewiki_page_stupid_diff($id, $data) {

    if (($old_ver = ($new_ver = $data["version"]) - 1) > 0)
    $data0 = ewiki_database("GET", array("id" => $id, "version" => $old_ver));

    (EWIKI_PRINT_TITLE) && ($o = "<h3>Differences between version $new_ver and $old_ver of ?{$id}?</h3>\n");

    $txt0 = preg_split("/\s*\n/", trim($data0["content"]));
    $txt2 = preg_split("/\s*\n/", trim($data["content"]));

    $diff0 = array_diff($txt0, $txt2);
    $diff2 = array_diff($txt2, $txt0);

    foreach ($txt2 as $i => $line) {

       $line = htmlentities($line);

       $i2 = $i;
       while ($rm = $diff0[$i2++]) {
          $o .= "<b>-</b><font color=\"#990000\"> <tt>$rm</tt></font><br>\n";
          unset($diff0[$i2-1]);
       }

       if (in_array($line, $diff2)) {
          $o .= "<b>+</b><font color=\"#009900\"> <tt>$line</tt></font><br>\n";
       }
       else {
          $o .= "&nbsp; $line<br>\n";
       }

    }

    foreach ($diff0 as $rm) {
       $o .= "<b>-</b><font color=\"#990000\"> <tt>$rm</tt></font><br>\n";
    }

    return($o);
 }


?>