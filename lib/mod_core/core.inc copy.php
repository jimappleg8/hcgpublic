<?php 

// =========================================================================
//  common_functions.php
//  part of The Hain Celestial Group Intranet
//  written by Jim Applegate
//  last modified: 29 April 2003
// =========================================================================


// -------------------------------------------------------------------------
// function displayErrMsg
//   Standard mechanism to print out an error message in HTML:
//
// -------------------------------------------------------------------------

function displayErrMsg($message)
{
	echo "<div align=\"center\">\n";
	echo "<span class=\"errorNotice\">" . $message;
	
	if (substr($message, (strlen($message) - 4), 4) == "<br>") {
		echo "&nbsp;</span>";
	} else {
		echo "<br>&nbsp;</span>";
	}
	
	echo "</div>\n";
}


// -------------------------------------------------------------------------
// function get_string
// Grabs a substring between two delimiters. After it finds the first instance
// of the first string, it finds the first instance of the second string in the
// remaining string.
// NOTE -- NO ERROR CHECKING IS DONE!
//
// -------------------------------------------------------------------------

function get_string($h, $s, $e)
{
	$sp = strpos($h, $s, 0) + strlen($s);
	$h = substr($h, $sp, strlen($h) - $sp); //strip the first part of the string
	$ep = strpos($h, $e, 0); 
	return substr($h, 0, $ep);
}


// -------------------------------------------------------------------------
// function getFile2Str
//   reads in contents of a text file and returns it as a string.
//
// -------------------------------------------------------------------------

function getFile2Str($filename)
{
   $fh = fopen($filename,'r') or die($php_errormsg);
   $contents = fread($fh,filesize($filename));
   fclose($fh) or die($php_errormsg);
   return $contents;

}

function text_to_html($text, $allowed_html = array())
{
   // perhaps test to see if the text has been run through htmlentities()
   // if not, run it through.
   
   // str_replace \n with \n<br>
   $text = str_replace("\n", "\n<br>", $text);
   
//   $urls = '(http|telnet|gopher|file|wais|ftp)';
   $ltrs = '\w';
   $gunk = '/#~:\.\?\+=&%@!\\-';
   $punc = '\.;\?\\-';
   $any = $ltrs.$gunk.$punc;
   
   // find any urls and convert to wiki format
//   $text = preg_replace('/[^(\|\s*)]((ht|f)tp:\/\/[^\s]+)[^(\s*\])]/', 
//                        '[$1 | $1]', $text);

   // find e-mails and replace with mailto: link
   $text = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', 
                        '<a href="mailto:$0">$0</a>', $text);
   
//   $text = preg_replace('/\b\[(['.$ltrs.'\s]+)\|(['.$any.'\s]+)\]/i',
//                        '<a href="$2">$1</a>', $text);

   // find any other links and make live
   $text = preg_replace('/((ht|f)tp:\/\/[^\s]+)/', 
                        '<a href="$1">$1</a>', $text);
                        
                 

   // reinstate allowed code. This assumes that the code has already
   // been run through htmlentities().
   foreach ($allowed_html as $allowed) {
      if (strtoupper($allowed) == "B") {
         $trans['&lt;b&gt;'] = "<b>";
         $trans['&lt;B&gt;'] = "<b>";
         $trans['&lt;/b&gt;'] = "</b>";
         $trans['&lt;/B&gt;'] = "</b>";
      } elseif (strtoupper($allowed) == "I") {
         $trans['&lt;i&gt;'] = "<i>";
         $trans['&lt;I&gt;'] = "<i>";
         $trans['&lt;/i&gt;'] = "</i>";
         $trans['&lt;/I&gt;'] = "</i>";      
      } elseif (strtoupper($allowed) == "U") {
         $trans['&lt;u&gt;'] = "<u>";
         $trans['&lt;U&gt;'] = "<u>";
         $trans['&lt;/u&gt;'] = "</u>";
         $trans['&lt;/U&gt;'] = "</u>";   
      } elseif (strtoupper($allowed) == "ALL") {
         $trans['&lt;'] = "<";
         $trans['&gt;'] = ">";
      }
   }
   $text = strtr($text, $trans);

   return $text;
}


?>