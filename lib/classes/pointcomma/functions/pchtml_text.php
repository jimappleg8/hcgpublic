<?php

/*////////////////////////////////////
//
// Formatting functions
//
////////////////////////////////////*/


function processHTMLText($string, $class='', $length=0) {
  if ($length>0) {
    $string = substr($string, 0, ($length+5)).'...';
  }
  $splitString = preg_split('/(<\/p>[^<]*)?<p(?!re).*?>/si', $string);
  if ($class != '') {
    $class = ' class="'.$class.'"';
  }
  for ($i=0;$i<count($splitString);$i++) {
    if (!empty($splitString[$i])) {
      $returnString .= '<p'.$class.'>'.preg_replace('%</p>%i', '', $splitString[$i])."</p>\n";
    }
  }
  return $returnString;
}

function mergeHTMLText($string, $words=0, $moreLink=false) {
  $exitLoop = false;
  $mergedString = preg_replace('/<br[^>]*>/si', ' &#149; ', $string);
  $splitString = preg_split('/(<\/p>[^<]*)?<p(?!re).*?>/si', $mergedString);
  $currentCount=0;
  for ($i=0;$i<count($splitString);$i++) {
    if (!empty($splitString[$i])) {
      if ($i < count($splitString) && $i > 1) {
        $returnString .= ' &#149; ';
      }
      $splitLine = split(' ', $splitString[$i]);
      if (count($splitLine) + $currentCount > $words && $words > 0) {
        $splitString[$i] = '';
        for ($j=0;$j<($words-$currentCount-1);$j++) {
          $splitString[$i] .= $splitLine[$j].' ';
        }
        $splitString[$i] .= $splitLine[$j].'</em></b></strong></i></a>... '.$moreLink;
        $exitLoop = true;
      } else {
        $currentCount += count($splitLine);
      }
      $returnString .= preg_replace('%</p>%i', '', $splitString[$i]);
    }
    if ($exitLoop) {
      return $returnString;
    }
  }
  return $returnString;
}

?>