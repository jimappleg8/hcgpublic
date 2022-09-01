<?php

 # this plugin converts the PhpWiki [[NoLink] to ErfurtWiki ![NoLink]
 # (fast)


 $ewiki_plugins["format_source"][] = "ewiki_format_source_emulate_phpwiki";


function ewiki_format_source_emulate_phpwiki (&$source) {

   $source = str_replace("[[", "![", $source);

}


?>