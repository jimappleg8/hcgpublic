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

// -------------------------------------------------------------------------
   
/**
 * Read File
 *
 * Opens the file specfied in the path and returns it as a string.
 *
 * @access	private
 * @param	string	path to file
 * @return	string
 */	
function read_file($file)
{
   if ( ! file_exists($file))
   {
      return FALSE;
   }
   
   if (function_exists('file_get_contents'))
   {
      return file_get_contents($file);      
   }

   if ( ! $fp = @fopen($file, 'rb'))
   {
      return FALSE;
   }
      
   flock($fp, LOCK_SH);

   $data = '';
   if (filesize($file) > 0)
   {
      $data =& fread($fp, filesize($file));
   }

   flock($fp, LOCK_UN);
   fclose($fp);

   return $data;
}

// -------------------------------------------------------------------------

function text_to_html($text, $allowed_html = array())
{
   // perhaps test to see if the text has been run through htmlentities()
   // if not, run it through.
   
   // str_replace \n with \n<br>
   $text = str_replace("\n", "\n<br>", $text);

   $ltrs = '\w';
   $gunk = '\#~:\.\?\+\/=&%@!\\-';
   $punc = '\.;\?';
   $any = $ltrs.$gunk.$punc;

   // look for full URLs (will also find them in wiki format)
   $pat = '/'.                  // begin pattern: look for...
          '\b'.                 // at a word boundary
          '(http|https|ftp)'.   // look for a protocol = $1
          '(:\/\/)'.            // colon, slash, slash = $2
          '(['.$ltrs.']+)'.     // one or more letters = $3
          '(['.$any.']+)'.      // one or more of any character = $4
          '(['.$any.']+)'.      // one or more of any character = $5
          '/';                  // end pattern
   $wikiformat = "[$1$2$3$4$5|$1$2$3$4$5]";
   $text = preg_replace($pat, $wikiformat, $text); 

   // remove doubled patterns where the URL was in a wiki format
   $pat = '/'.                  // begin pattern: look for...
          '\|'.                 // a vertical line
          '\s*'.                // zero or more spaces
          '\['.                 // an open bracket
          '(http|https|ftp)'.   // one of three protocols = $1
          '(:\/\/)'.            // colon, slash, slash = $2
          '(['.$ltrs.']+)'.     // one or more letters = $3
          '(['.$any.']+)'.      // one or more of any character = $4
          '/';                  // end pattern
   $text = preg_replace($pat, '', $text);

   // replace double closing brackets with a single bracket
   $pat = '/\]\]/';       
   $text = preg_replace($pat, ']', $text);

   // format wiki-style links to HTML links
   $pat = '/'.                  // begin pattern: look for...
          '\['.                 // an opening bracket
          '(['.$any.'\s]+)'.    // one or more chars or spaces = $1
          '\|'.                 // a vertical line
          '\s*'.                // zero or more spaces
          '(http|https|ftp)*'.  // opt: one of three protocols = $2
          '(:\/\/)*'.           // opt: colon, slash, slash = $3
          '(['.$ltrs.']+)*'.    // opt: one or more letters = $4
          '(['.$any.']+)'.      // one or more of any character = $5
          '\s*'.                // zero or more spaces
          '\]'.                 // a closing bracket
          '/';                  // end pattern
   $replacement = '<a href="$2$3$4$5" target=\"_blank\">$1</a>';
   $text = preg_replace($pat, $replacement, $text);

   // find e-mails and replace with mailto: link
   $text = preg_replace('/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i', 
                        '<a href="mailto:$0">$0</a>', $text);                 

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