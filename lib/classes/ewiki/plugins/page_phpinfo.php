<?php

# will produce garbage, if output buffering is already used by your PHP
# interpreter or yoursite.php


$ewiki_plugins["page"]["PhpInfo"] = "ewiki_page_phpinfo";


function ewiki_page_phpinfo($id, $data, $action) {
   ob_start();
   phpinfo(45);
   $o .= ob_get_gontents();
   ob_end_clean();
   $o = substr($o, 554, -19);
   return($o);
}


?>