#!/usr/bin/php
<?php
// ewiki2doku.php

/* --------------------------------------------------------------------------

Below PHP script is a minor attempt in converting  ErfurtWiki (ewiki) pages to DokuWiki format. It is a mod of moinmoin2doku (thanks!). It has not been tested much, it was only used to convert one ewiki installation of about 100 pages. After the conversion the new pages still need manual editing.

Requirements

   * php (on the command line)
   * ewiki flat files (if you use ewiki with a SQL database you first need to export the pages to plain text files)

Capabilities

   It is able to transform
   * Headings
   * Links (most, including most CamelCase)
   * Bold/italic/monospaced/teletype
   * big/small text (markup removed)
   * Lists
   * <pre> code blocks
   * InterWiki links (see code and customize for your needs)

Missing features/bugs

   It is not able to transform
    * (probably quite some; please test first and use at your own risk only)

   -------------------------------------------------------------------------- */
 
// Use at your own risk! No warranty implied!
 
//check command line parameters
if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
  echo "\n  Converts all files from given directory\n";
  echo "  from ErfurtWiki to DokuWiki syntax. NOT RECURSIVE\n\n";
  echo "  Usage:\n";
  echo "  ".$argv[0]." <input dir> <output dir>\n\n";
} 
else {
  //get input and output directories
  $inDir = realpath($argv[1]) or die("input dir error");
  $outDir = realpath($argv[2]) or die("output dir error");
  //just print information
  echo "\nInput Directory: ".$inDir."\n";
  echo "Output Directory: ".$outDir."\n\n";
 
  //get all files from directory
  if (is_dir($inDir)) {
    $files = filesFromDir($inDir);
  }
 
  //migrate each file
  foreach ($files As $file) {
    //convert filename
    $ofile = convFileNames($file);
    //just print information
    echo "Migrating from ".$inDir."/".$file." to ".$outDir."/".$ofile."\n";
 
    //read input file
    $text = readFl($inDir."/".$file);
 
    //convert content
    $text = ewiki2doku($text);
 
    //encode in utf8
    $text = utf8_encode($text);
 
    //write output file
    writeFl($outDir."/".$ofile, $text);
  }
}
 
function ewiki2doku($text) {
 
  //line by line
  $lines = explode("\n", $text);
  foreach($lines As $line) {
    //start converting
    $find = Array(  
       '/\[notify: ?[^ ]*\]/',         //remove [notify:...]
       '/\[jump:([^]]+)\]/',           //[jump:...]
       '/^    *([^ ])/',               //indented paragraphs (we always used 4 spaces but also [tab] is allowed
       '/%%%/',                        //newline
       '/([^!~=|[])(\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b)(([^]|#])|$)/',  //CamelCase, dont change if CamelCase is in InternalLink
       '/([^!~]|^)\[([^] |[]+)\]/',    //internal link
       '/\[([^]|[]+)\|([^]|[]+)\]/',   //external links and links with |
       '/\["([^"]+)" ([^ ]+)\]/',      //Ewiki ["..." ...] style links ([... "..."] not recognized)
       '/\[\[([^ :]+):([^]\/@]+)\]\]/', //InterWiki link (the /@ tries to exclude http:// and mailto:)
       '/\[\[(([^] |[]+)\.(png|jpe?g|gif))\]\]/', //image link (only some)
       '/<pre>/',                      //pre open
       '/<\/pre>/',                    //pre close
       '/^\* /',                       //lists 1
       '/^\*\* /',                     //lists 2
       '/^\*\*\* /',                   //lists 3
       '/^# /',                        //ordered lists 1
       '/^## /',                       //ordered lists 2
       '/^### /',                      //ordered lists 3
       '/^!{3} ?(.*)$/',               //heading 1
       '/^!{2} ?(.*)$/',               //heading 2
       '/^!{1} ?(.*)$/',               //heading 3
       '/__([^_]+)__/',                //bold 1
       '/\*\*([^*]+)\*\*/',            //bold 2
       '/\'\'([^\']+)\'\'/',           //italic (emphasize)
       '/==(([^= ][^=]+)|[^=])==/',    //monospaced (also taking care of ==X==)
       '/<tt>(.+)<\/tt>/',             //teletype
       '/##([^#]+)##/',                //big text
       '/µµ([^µ]+)µµ/',                //small text
       '/[!~](\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b)/', //~CamelCase + !CamelCase
       '/[!~](\[[^][]+\])/',           //~[text] + !text (just remove ~ and !)
       '/<cc>(\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b)<\/cc>/', //CamelCase, dont change if CamelCase is in InternalLink
       '/^(=+ .*)\[\[(.*)\]\](.* =+)$/',   //remove links in headlines
       '/([^<:!~]|^)(\b[-A-Za-z0-9+_.]+@[-A-Za-z0-9_]+\.[-A-Za-z0-9_.]+[A-Za-z]\b)([^>]|$)/', //email addresses
       '/^keywords: /',                //misc1
       '/\[\[ManPages>/',              //misc2
       '/\[\[WikiPedia>/'              //misc3
       );
    $replace = Array(
       '',                             //remove [notify:...]
       'Please go to [${1}]',          //[jump:...]
       '> ${1}',                       //indented paragraphs
       '\\\\\\ ',                      //newline
       '${1}<cc>${2}</cc>${3}',        //CamelCase (preparation, see below for finish)
       '${1}[[${2}]]',                 //internal link
       '[[${2}| ${1}]]',               //external link and links with |
       '[[${2}| ${1}]]',               //Ewiki ["..." ...] style links
       '[[${1}>${2}]]',                //InterWiki link
       '{{${1}}}',                     //images link
       '<code>',                       //(<pre>) code open
       '</code>',                     //(</pre>)code close - remove space between < and /, it is included for viewing in dokuwiki
       '  * ',                         //lists 1
       '    * ',                       //lists 2
       '      * ',                     //lists 3
       '  - ',                         //ordered lists 1
       '    - ',                       //ordered lists 2
       '      - ',                     //ordered lists 3
       '====== ${1} ======',           //heading 1
       '===== ${1} =====',             //heading 2
       '==== ${1} ====',               //heading 3
       '**${1}**',                     //bold 1
       '**${1}**',                     //bold 2                     
       '//${1}//',                     //italic (emphasize)
       '\'\'${1}\'\'',                 //monospaced
       '\'\'${1}\'\'',                 //teletype
       '**${1}**',                     //big text -- no markup in dokuwiki
       '${1}',                         //small text -- no markup in dokuwiki
       '${1}',                         //~CamelCase + !CamelCase
       '${1}',                         //~[text] + !text (just remove ~ and !)
       '[[${1}]]',                     //CamelCase, finish <cc>CamelCase</cc>
       '${1}${2}${3}',                 //remove links in headlines
       '${1}<${2}>${3}',               //email addresses
       '**keywords:** ',               //misc1
       '[[man>',                       //misc2
       '[[wp>'                         //misc3
       );
    $line = preg_replace($find,$replace,$line);
 
    $ret = $ret.$line."\n";
  }
  return $ret;
}
 
function convFileNames($name) {
  /* ö,ä,ü, ,. and more
  */
  $find = Array('/_20/',
                '/_5f/',
                '/_2e/',
                '/_c4/',
                '/_f6/',
                '/_fc/',
                '/_26/',
                '/_2d/'
                );
  $replace = Array('_',
                   '_',
                   '_',
                   'ae',
                   'oe',
                   'ue',
                   '_',
                   '-'
                   );
  $name = preg_replace($find,$replace,$name);
  $name = strtolower($name);
  return $name.".txt";
}
 
 
function filesFromDir($dir) {
  $files = Array();
  $handle=opendir($dir);
  while ($file = readdir ($handle)) {
     if ($file != "." && $file != ".." && !is_dir($dir."/".$file)) {
         array_push($files, $file);
     }
  }
  closedir($handle); 
  return $files;
}
 
function readFl($file) {
  $fr = fopen($file,"r");
  if ($fr) {
    while(!feof($fr)) {
      $text = $text.fgets($fr);
    }
    fclose($fr);
  }
  return $text;
}
 
function writeFl($file, $text) {
  $fw = fopen($file, "w");
  if ($fw) {
    fwrite($fw, $text);
  }
  fclose($fw);
}
 
?>