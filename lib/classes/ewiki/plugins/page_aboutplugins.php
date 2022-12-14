<?php

#--  show infos about registered plugins (even internal plugins)
#


$ewiki_plugins["page"]["AboutPlugins"] = "ewiki_page_aboutplugins";



function ewiki_page_aboutplugins($id, $data, $action) {

   global $ewiki_plugins;

   (EWIKI_PRINT_TITLE) and ($o .= "<h2>$id</h2>\n");

   #-- plugin types
   foreach (array("page", "action", "mpi") as $pclass) {

      $o .= "<u>$pclass plugins</u><br>\n";

      switch ($pclass) {
         case "page":
            $o .= "dynamically generated pages<br>\n";
            break;
         case "action":
            $o .= "can be activated on each (real) page<br>\n";
            break;
         case "mpi":
            $o .= "the markup plugins can be utilized to integrate dynamic content into pages<br>\n";
            break;
         default:
      }

      if ($pf_a = $ewiki_plugins[$pclass]) {
         foreach ($pf_a as $i=>$pf) {

            switch ($pclass) {
               case "page":
                  $i = '<a href="'.ewiki_script("",$i).'">'.$i.'</a>';
                  break;
               case "action":
                  $i = '<a href="'.ewiki_script($i,"Notepad").'">'.$i.'</a>';
                  break;
               case "mpi":
                  $i = '<a href="'.ewiki_script("mpi/$i").'">&lt;?plugin '.$i.'?&gt;</a>';
                  break;
               default:
            }

            $o .= "? <b>$i</b> <small>via $pf</small><br>\n";

         }
      }

      $o .= "<br>\n";

   }

   #-- task plugins
   $o .= "<u>task plugins</u> (core stuff)<br>\n";
   $o .= "enhance the wiki engine internally, with widely varying functionality enhancements or changes<br>\n";
   foreach ($ewiki_plugins as $i=>$a) {
      if (is_array($a)) {
         foreach ($a as $n=>$pf) {

            if (is_int($n)) {

               $o .= "? <b><tt>$i</tt></b> <small>via $pf</small><br>\n";

            }
         }
      }
   }
   $o .= "<br>\n";


   return($o);

}


?>