<?php

#
#  the otherwise invisible markup [notify:you@there.net] will trigger a
#  mail, whenever a page is changed
#



$ewiki_plugins["edit_hook"][] = "ewiki_notify_edit_hook";
$ewiki_plugins["format_source"][] = "ewiki_format_remove_notify";



function ewiki_notify_edit_hook($id, $data, &$hidden_postdata) {

   if ($_REQUEST["save"]) {

      $mailto = ewiki_notify_links($data["content"], 0);

      if (count($mailto)) {

         ($server = $_SERVER["HTTP_HOST"]) or
         ($server = $_SERVER["SERVER_NAME"]);
         $s_2 = EWIKI_PAGE_INDEX;
         $s_3 = $_SERVER["SERVER_ADMIN"];
         $s_4 = "http://" . $server . $_SERVER["REQUEST_URI"];
         $link = str_replace("edit/$id", "$id", $s_4);

         $m_text = "Hi,\n\n"
               . "A WikiPage has changed, and you requested to get notified, when this\nhappens.\n\n"
               . "The changed page was '$id' and can be found under following URL:\n$link\n\n"
               . "To stop messages like this please strip the [notify:...] with your address\nfrom the page edit box at $s_4\n\n"
               . "($s_2 on http://$server/)\n$s_3\n";

         $m_from = "ewiki@$server";
         $m_subject = "$id has changed [notify:...]";

         $m_to = implode(", ", $mailto);

         mail($m_to, $m_subject, $m_text, "From: \"$s_2\" <$m_from>\nX-Mailer: ErfurtWiki/".EWIKI_VERSION);

      }
   }
}



function ewiki_notify_links(&$source, $strip=1) {
   $links = array();
   $l = 0;
   if (strlen($source) > 10)
   while (($l = @strpos($source, "[notify:", $l)) !== false) {
      $r = strpos($source, "]", $l);
      $str = substr($source, $l, $r + 1 - $l);
      if (!strpos("\n", $str)) {
         $links[] = trim(substr($str, 8, -1));
         if ($strip) {
            $source = substr($source, 0, $l) . substr($source, $r + 1);
         }
      }
      $l++;
   }
   return($links);
}



function ewiki_format_remove_notify(&$source) {
   ewiki_notify_links($source, 1);
}



?>