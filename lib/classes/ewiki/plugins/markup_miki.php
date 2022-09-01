<?php

 # this plugins emulates the markup found in »miki«, but allows for
 # other constructs where open and end markup differs

 # do not use it unless required, because these plugins usually slow
 # down the format() call


$ewiki_plugins["format_line"][] = "ewiki_format_line_emulate_miki";


function ewiki_format_line_emulate_miki (&$o, &$line, &$post) {

   $wm_oe = array(
      "[_ _]" => array("<u>", "</u>"),
      "[* *]" => array("<b>", "</b>"),
      "[/ /]" => array("<i>", "</i>"),
   );

   foreach ($wm_oe as $f_ => $replace) {
 
      $find0 = strtok($f_, " ");
      $find1 = strtok(" ");
      $n0 = strlen($find0);
      $n1 = strlen($find1);

      $loop = 20;

      while(($loop--) && (($l = strpos($line, $find0)) !== false) && ($r = strpos($line, $find1, $l + $n0))) {

            $line = substr($line, 0, $l) . $replace[0] .
                    substr($line, $l + $n0, $r - $l - $n0) .
                    $replace[1] . substr($line, $r + $n1);
      }

  }

}