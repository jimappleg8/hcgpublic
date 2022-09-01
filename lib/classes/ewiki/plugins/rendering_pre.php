<?php

/*
  the plugin pre-handles <pre>-textareas, and thus reduces garbaging
  of most <code> and <pre> text pieces
*/


$ewiki_plugins["render"][0] = "ewiki_format_pre_pre";


function ewiki_format_pre_pre($wsrc, $sl=1, $hl=EWIKI_ALLOW_HTML, $sh=0) {

   $loop = 20;

   while (strlen($wsrc) && ($loop--)) {

      list($c, $wsrc) = preg_split('/\n{4,5}/', $wsrc, 2);
      if ($c) {
         $html .= ewiki_format($c, $sl,$hl,$sh);
      }

      list($c, $wsrc) = preg_split('/\n\n\n/', $wsrc, 2);
      if ($c) {
         $html .= "<pre>".ewiki_format_pre_pre_escape($c)."\n</pre>";
      }

   }

   return($html);

}



function ewiki_format_pre_pre_escape($code) {
   $code = trim($code);
/*
   if (strstr($code, "<?") || strstr($code, "array(") || strstr($code, "</")
       || strstr($code, "($") && strstr($code, ");")
      )
   {
      return(highlight_string($code));
   }
*/
   return(htmlentities($code));
}


?>