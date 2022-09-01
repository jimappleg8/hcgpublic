<?php

include "class.hlight.php";

// code goes here...

// code to tell when to show the source..

$script = $_SERVER["SCRIPT_FILENAME"];
$fp = fopen($script,"r");
$contents = fread($fp, filesize($script));
fclose($fp);

// this code was from one of my own projects, replace $dsn
// to any var that you want to be protected during the
// showing of the source

$contents = preg_replace('{(\\$db.*?) = ".*?";}',
'$1="*****";', $contents);

$highlighter = new SyntaxHighlighter_php;
$contents = $highlighter->highlight($contents);

echo $contents;
?>
